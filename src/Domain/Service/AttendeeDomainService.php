<?php

namespace App\Domain\Service;

use App\Application\Request\AttendeeRequest;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Product;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AttendeeDomainService
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function createAttendee(AttendeeRequest $attendeeRequest, Product $product): Attendee
    {
        $attendee = new Attendee(
            name: $attendeeRequest->name,
            firstName: $attendeeRequest->firstName,
            middleName: $attendeeRequest->middleName,
            familyName: $attendeeRequest->familyName,
            nickName: $attendeeRequest->nickName,
            email: $attendeeRequest->email,
            orderCode: $attendeeRequest->orderCode,
            ticketId: $attendeeRequest->ticketId,
            ticketSecret: $attendeeRequest->ticketSecret,
            product: $product, nfcTagId: $attendeeRequest->nfcTagId,
            miniIdentifier: $attendeeRequest->miniIdentifier,
        );

        $hashedPassword = $this->passwordHasher->hashPassword($attendee, '0000');
        $attendee->setPassword($hashedPassword);

        return $attendee;
    }

    public function updateAttendee(Attendee $attendee, AttendeeRequest $attendeeRequest): Attendee
    {
        return $attendee->setName($attendeeRequest->name)
                        ->setFirstName($attendeeRequest->firstName)
                        ->setMiddleName($attendeeRequest->middleName)
                        ->setFamilyName($attendeeRequest->familyName)
                        ->setNickName($attendeeRequest->nickName)
                        ->setEmail($attendeeRequest->email)
                        ->setNfcTagId($attendeeRequest->nfcTagId);
    }
}
