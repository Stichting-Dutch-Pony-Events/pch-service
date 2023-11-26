<?php

namespace App\DataAccessLayer\Pretix\Views;

use Illuminate\Support\Carbon;
use IntVent\EBoekhouden\Client;
use IntVent\EBoekhouden\Filters\RelationFilter;
use IntVent\EBoekhouden\Models\EboekhoudenRelation;

use function DataAccessLayer\Pretix\Views\mb_convert_encoding;

class InvoiceAddress
{
    public ?Carbon $lastModified;
    public ?string $company;
    public bool $isBusiness;
    public ?string $name;
    public ?string $street;
    public ?string $zipCode;
    public ?string $city;
    public ?string $country;
    public ?string $state;
    public ?string $internalReference;
    public ?string $vatId;

    public function __construct($invAddObj)
    {
        try { $this->lastModified = Carbon::parse($invAddObj->lastModified); } catch (\Exception) { $this->lastModified = null; }
        $this->company = $invAddObj->company;
        $this->isBusiness = $invAddObj->is_business;
        $this->name = $invAddObj->name;
        $this->street = $invAddObj->street;
        $this->zipCode = $invAddObj->zipcode;
        $this->city = $invAddObj->city;
        $this->country = $invAddObj->country;
        $this->state = $invAddObj->state;
        $this->internalReference = $invAddObj->internal_reference;
        $this->vatId = $invAddObj->vat_id;
    }

    public function getRelationCode() {
        $relCode = strtoupper(('PT' . $this->country . $this->zipCode));
        if(strlen($relCode) > 8)
            $relCode = substr($relCode, 0, 8);
        if($this->company !== null && $this->company !== '') {
            $relCode .= '_' . (strlen($this->company) <= 6 ? $this->company : substr($this->company, 0, 6));
        } else {
            $nameParts = explode(' ', $this->name);
            if(count($nameParts) > 1) {
                $relCode .= '_';
                $lastName =  $nameParts[count($nameParts) - 1];
                $firstName = $nameParts[0];
                $relCode .= (strlen($lastName) <= 3 ? $lastName : substr($lastName, 0, 3));
                $relCode .= (strlen($firstName) <= 3 ? $firstName : substr($firstName, 0, 3));
            } else {
                $relCode .= '_' . (strlen($this->name) <= 6 ? $this->name : substr($this->name, 0, 6));
            }
        }
        return mb_convert_encoding(strlen($relCode) <= 15 ? $relCode : substr($relCode, 0, 15), 'UTF-8');
    }

    function Unaccent($string)
    {
        if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false)
        {
            $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
        }
        return $string;
    }

    public function getEboekRelation() : ?EboekhoudenRelation {
        $eboekClient = new Client(env('EBOEKHOUDEN_USERNAME'), env('EBOEKHOUDEN_CODE_1'), env('EBOEKHOUDEN_CODE_2'));

        $relations = $eboekClient->getRelations((new RelationFilter())->setCode($this->getRelationCode()));
        if(count($relations) > 0) {
            /** @var EboekhoudenRelation $relation */
            $relation =  $relations[0];
            $changed = false;
            if($this->company !== null && $this->company !== '') {
                if($relation->getRelationType() !== 'B') {
                    $relation->setRelationType('B');
                    $changed = true;
                }
                if($relation->getCompany() !== $this->company) {
                    $relation->setCompany($this->company);
                    $changed = true;
                }
                if($relation->getContact() !== $this->name) {
                    $relation->setContact($this->name);
                    $changed = true;
                }
            } else {
                echo $this->getRelationCode();
                echo gettype($relation);
                if($relation->getRelationType() !== 'P') {
                    $relation->setRelationType('P');
                    $changed = true;
                }
                if($relation->getCompany() !== $this->name) {
                    $relation->setCompany($this->name);
                    $changed = true;
                }
            }
            if($relation->getAddress() !== $this->street) {
                $relation->setAddress($this->street);
                $changed = true;
            }
            if($relation->getZipcode() !== $this->zipCode) {
                $relation->setZipcode($this->zipCode);
                $changed = true;
            }
            if($relation->getCity() !== $this->city) {
                $relation->setCity($this->city);
                $changed = true;
            }
            if($relation->getCountry() !== $this->country) {
                $relation->setCountry($this->country);
                $changed = true;
            }
            if($relation->getVatNumber() !== $this->vatId) {
                $relation->setVatNumber($this->vatId);
                $changed = true;
            }
            if($changed) {
                $eboekClient->updateRelation($relation);
            }
            return $relation;
        } else {
            $relation = new EboekhoudenRelation();
            if($this->company !== null && $this->company !== '') {
                $relation->setRelationType('B');
                $relation->setCompany($this->company);
                $relation->setContact($this->name);
            } else {
                $relation->setRelationType('P');
                $relation->setCompany($this->name);
            }
            $relation->setCode($this->getRelationCode());
            $relation->setAddress($this->street);
            $relation->setZipcode($this->zipCode);
            $relation->setCity($this->city);
            $relation->setCountry($this->country);
            $relation->setVatNumber($this->vatId);
            $eboekClient->addRelation($relation);
            return $relation;
        }
        return null;
    }
}
