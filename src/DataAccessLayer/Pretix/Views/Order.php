<?php

namespace App\DataAccessLayer\Pretix\Views;

use App\Api\PayPal\PayPalApi;
use App\Api\PretixApi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use IntVent\EBoekhouden\Client;
use IntVent\EBoekhouden\Filters\MutationFilter;
use IntVent\EBoekhouden\Models\EboekhoudenMutation;
use IntVent\EBoekhouden\Models\EboekhoudenMutationLine;
use IntVent\EBoekhouden\Models\EboekhoudenRelation;
use Stripe\StripeClient;

use function DataAccessLayer\Pretix\Views\str_contains;
use function DataAccessLayer\Pretix\Views\str_starts_with;

class Order
{
    public string $code;
    public string $status;
    public string $email;
    public bool $testMode;
    public ?Carbon $datetime;
    public ?Carbon $expires;
    public ?string $total;

    /** @var OrderPosition[] $positions */
    public ?array $positions;

    /** @var OrderPayment[] $payments */
    public ?array $payments;
    /** @var  OrderRefund[] $refunds */
    public ?array $refunds;
    public ?Carbon $lastModified;

    public ?InvoiceAddress $invoiceAddress;

    public function __construct($orderObj)
    {
        $this->code = $orderObj->code;
        $this->status = $orderObj->status;
        $this->testMode = $orderObj->testmode;
        $this->email = $orderObj->email;
        try { $this->datetime = Carbon::parse($orderObj->datetime); } catch (\Exception) { $this->datetime = null; }
        try { $this->expires = Carbon::parse($orderObj->expires); } catch (\Exception) { $this->expires = null; }
        $this->total = $orderObj->total;
        if(is_array($orderObj->payments)) {
            $this->payments = [];
            foreach ($orderObj->payments as $payment) {
                $this->payments[] = new OrderPayment($payment);
            }
        } else {
            $this->payments = null;
        }

        if(is_array($orderObj->refunds)) {
            $this->refunds = [];
            foreach ($orderObj->refunds as $refund) {
                $this->refunds[] = new OrderRefund($refund);
            }
        } else {
            $this->refunds = null;
        }

        if(is_array($orderObj->positions)) {
            $this->positions = [];
            foreach ($orderObj->positions as $position) {
                $this->positions[] = new OrderPosition($position);
            }
        }

        try { $this->lastModified = Carbon::parse($orderObj->last_modified); } catch (\Exception) { $this->lastModified = null; }
        $this->invoiceAddress = new InvoiceAddress($orderObj->invoice_address);
    }

    public function getConfirmedPaymentCount() {
        $confirmedCount = 0;
        foreach ($this->payments as $payment) {
            if($payment->state === 'confirmed')
                $confirmedCount++;
        }
        return $confirmedCount;
    }

    public function getConfirmedPayments() {
        $confirmedPayments = [];
        foreach ($this->payments as $payment) {
            if($payment->state === 'confirmed')
                $confirmedPayments[] = $payment;
        }
        return $confirmedPayments;
    }

    public function importInEboekhouden(PretixApi $api = null) {
        if($api === null)
            $api = new PretixApi();
        /** @var Invoice[] $invoices */
        $invoices = $api->getInvoices($this->code);
        /** @var OrderPayment[] $confirmedPayments */
        $confirmedPayments = $this->getConfirmedPayments();
        if(count($invoices) !== 1 || count($confirmedPayments) > 1)
            return false;

        $invoice = $invoices[0];
        $payment = null;
        if(count($confirmedPayments) > 0) {
            $payment = $confirmedPayments[0];
        }
        try {
        $eboekRelation = $this->invoiceAddress->getEboekRelation();

        $eboekClient = new Client(env('EBOEKHOUDEN_USERNAME'), env('EBOEKHOUDEN_CODE_1'), env('EBOEKHOUDEN_CODE_2'));
        /** @var EboekhoudenMutation[] $mutations */

            $mutations = $eboekClient->getMutations((new MutationFilter())->setInvoiceNumber($invoice->number));


            $invoiceMutation = null;
            $paymentMutation = null;

            foreach ($mutations as $mutation) {
                if ($mutation->getKind() === 'FactuurVerstuurd')
                    $invoiceMutation = $mutation;
                elseif ($mutation->getKind() === 'FactuurbetalingOntvangen')
                    $paymentMutation = $mutation;
            }

            if ($invoiceMutation === null) {
                $this->importInvoice($invoice, $eboekRelation, $api, $eboekClient);
            }

            if ($paymentMutation === null && $payment !== null) {
                $paymentImport = $this->importPayment($payment, $invoice, $eboekRelation, $eboekClient);
                if (!$paymentImport)
                    return false;
            }

            if ($payment !== null) {
                $paymentId = $payment->getPaymentId();
                if ($paymentId !== null) {
                    $this->importPaymentFee($eboekClient, $payment);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return false;
        }
    }

    private function importInvoice(Invoice $invoice, EboekhoudenRelation $eboekRelation, PretixApi $api, Client $eboekClient) {
        try {
            $invoiceMutation = new EboekhoudenMutation();
            $invoiceMutation
                ->setKind('FactuurVerstuurd')
                ->setDate($invoice->date->toDate())
                ->setLedgerCode(env('DEBITEUREN_GROOTBOEK'))
                ->setInvoiceNumber($invoice->number)
                ->setDescription('PonyCon Holland 2022 - ' . $invoice->order . ' - Invoice ' . $invoice->number . ' - ' . $invoice->invoiceToName)
                ->setJournal($invoice->order)
                ->setPaymentTerm(14)
                ->setInOrExVat('IN')
                ->setRelationCode($eboekRelation->getCode());

            foreach ($invoice->lines as $line) {
                $eboekLine = new EboekhoudenMutationLine();
                if ($line->item !== null) {
                    $item = $api->getItem($line->item);
                    if ($item === null) {
                        $eboekLine->setLedgerCode(env('DEFAULT_GROOTBOEK'));
                    } else {
                        if ($item->metaData !== null && property_exists($item->metaData, 'grootboek')) {
                            $eboekLine->setLedgerCode($item->metaData->grootboek);
                        } else {
                            $eboekLine->setLedgerCode(env('DEFAULT_GROOTBOEK'));
                        }
                    }
                } elseif ($line->feeType === 'payment') {
                    $eboekLine->setLedgerCode(env('PAYPAL_COSTS_GROOTBOEK'));
                } else {
                    continue;
                }
                $eboekLine->setEntryAmount($line->grossValue);
                $eboekLine->setVatAmount($line->taxValue);
                $eboekLine->setAmountInclVat($line->grossValue);
                $eboekLine->setAmountExclVat($line->grossValue - $line->taxValue);
                if (abs($line->taxRate - 21.0) < 0.00001) {
                    $eboekLine->setVatCode('HOOG_VERK_21');
                    $eboekLine->setVatPercentage(21.0);
                } elseif (abs($line->taxRate - 9.0) < 0.00001) {
                    $eboekLine->setVatCode('LAAG_VERK_9');
                    $eboekLine->setVatPercentage(9.0);
                } else {
                    continue;
                }
                $invoiceMutation->addLine($eboekLine);
            }
            $eboekClient->addMutation($invoiceMutation);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
        }
    }

    private function importPayment(OrderPayment $payment, Invoice $invoice, EboekhoudenRelation $eboekRelation, Client $eboekClient) {
        try {
            $paymentMutation = new EboekhoudenMutation();
            $paymentId = $payment->getPaymentId();
            $paymentMutation
                ->setKind('FactuurbetalingOntvangen')
                ->setDate($payment->paymentDate->toDate())
                ->setInvoiceNumber($invoice->number)
                ->setRelationCode($eboekRelation->getCode())
                ->setDescription("Payment for PCH 22 - Invoice " . $invoice->number . ' - Order ' . $this->code . ($paymentId !== null ? (' - Payment ID: ' . $paymentId) : ''))
                ->setJournal($this->code)
                ->setPaymentTerm(0)
                ->setInOrExVat("IN");

            if (str_contains($payment->provider, 'stripe')) {
                $paymentMutation->setLedgerCode(env('STRIPE_ACCOUNT_GROOTBOEK'));
            } else if (str_contains($payment->provider, 'paypal')) {
                $paymentMutation->setLedgerCode(env('PAYPAL_ACCOUNT_GROOTBOEK'));
            } else {
                return false;
            }

            $line = new EboekhoudenMutationLine();
            $line->setEntryAmount($payment->amount)
                ->setAmountExclVat($payment->amount)
                ->setAmountInclVat($payment->amount)
                ->setVatPercentage(0)
                ->setVatCode('GEEN')
                ->setLedgerCode(env('DEBITEUREN_GROOTBOEK'))
                ->setInvoiceNumber($invoice->number);

            $paymentMutation->addLine($line);
            $eboekClient->addMutation($paymentMutation);

            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return false;
        }
    }

    private function importPaymentFee(Client $eboekClient, OrderPayment $payment) {
        try {
            $mutations = $eboekClient->getMutations((new MutationFilter())->setDateFrom($payment->paymentDate->toDate())->setDateTo($payment->paymentDate->toDate()));


            $paymentId = $payment->getPaymentId();
            if ($paymentId === null || $paymentId === '')
                return false;

            /** @var EboekhoudenMutation $mutation */
            foreach ($mutations as $mutation) {
                if ($mutation->getKind() === "GeldUitgegeven" && str_contains($mutation->getDescription(), $this->code) && str_contains($mutation->getDescription(), $paymentId) && str_contains($mutation->getDescription(), 'TransactionFee')) {
                    return true;
                }
            }

            $newMutation = new EboekhoudenMutation();
            $newMutation->setKind('GeldUitgegeven')
                ->setDate($payment->paymentDate->toDate())
                ->setJournal($this->code)
                ->setDescription('TransactionFee - Order: ' . $this->code . ' - Payment ID: ' . $paymentId)
                ->setInOrExVat('IN');

            if (str_contains($payment->provider, 'stripe')) {
                $newMutation->setLedgerCode(env('STRIPE_ACCOUNT_GROOTBOEK'));
                $stripe = new StripeClient(env('STRIPE_KEY'));
                $charge = null;
                if (str_starts_with($paymentId, 'pi')) {
                    $charges = $stripe->paymentIntents->retrieve($paymentId, [])->charges->data;
                    foreach ($charges as $piCharge) {
                        if ($piCharge->paid) {
                            $charge = $piCharge;
                            break;
                        }
                    }
                } else {
                    $charge = $stripe->charges->retrieve($paymentId, []);
                }
                if ($charge === null)
                    return false;
                $balanceTransaction = $stripe->balanceTransactions->retrieve($charge->balance_transaction);
                $amount = ((float)$balanceTransaction->fee) * 0.01;
                $mutationLine = new EboekhoudenMutationLine();
                $mutationLine->setEntryAmount($amount)
                    ->setAmountInclVat($amount)
                    ->setAmountExclVat($amount)
                    ->setVatCode('GEEN')
                    ->setLedgerCode(env('STRIPE_COSTS_GROOTBOEK'))
                    ->setVatPercentage(0);
                $newMutation->addLine($mutationLine);
            } elseif (str_contains($payment->provider, 'paypal')) {
                $paypal = new PayPalApi();
                $payPalPayment = $paypal->getPayment($paymentId);
                if (property_exists($payPalPayment, 'seller_receivable_breakdown')) {
                    if (property_exists($payPalPayment->seller_receivable_breakdown, 'paypal_fee') && property_exists($payPalPayment->seller_receivable_breakdown->paypal_fee, 'value')) {
                        $amount = (float)$payPalPayment->seller_receivable_breakdown->paypal_fee->value;
                        if ($amount <= 0.000000001)
                            return false;
                        $newMutation->setLedgerCode(env('PAYPAL_ACCOUNT_GROOTBOEK'));
                        $mutationLine = new EboekhoudenMutationLine();
                        $mutationLine->setEntryAmount($amount)
                            ->setAmountInclVat($amount)
                            ->setAmountExclVat($amount)
                            ->setVatCode('GEEN')
                            ->setLedgerCode(env('PAYPAL_COSTS_GROOTBOEK'))
                            ->setVatPercentage(0);
                        $newMutation->addLine($mutationLine);
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            $eboekClient->addMutation($newMutation);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return false;
        }
    }
}
