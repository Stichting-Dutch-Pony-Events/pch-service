<?php

namespace App\Domain\Service;

use App\Application\Request\AttendeeRequest;
use App\Application\Request\SetPasswordRequest;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Product;
use App\Util\Exceptions\Exception\Common\InvalidInputException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class AttendeeDomainService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
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
            product: $product,
            tShirtSize: $attendeeRequest->tShirtSize,
            nfcTagId: $attendeeRequest->nfcTagId,
            miniIdentifier: $attendeeRequest->miniIdentifier,
        );

        $hashedPassword = $this->passwordHasher->hashPassword($attendee, '0000');
        $attendee->setPassword($hashedPassword);

        $attendee->setRoles([$product->getDefaultRole()]);

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

    public function updatePassword(Attendee $attendee, SetPasswordRequest $setPasswordRequest): Attendee
    {
        if ($this->passwordHasher->isPasswordValid($attendee, $setPasswordRequest->currentPassword)) {
            throw new InvalidInputException("Password Incorrect");
        }

        if ($setPasswordRequest->password !== $setPasswordRequest->passwordConfirmation) {
            throw new InvalidInputException("Passwords are not the same");
        }

        $hashedPassword = $this->passwordHasher->hashPassword($attendee, $setPasswordRequest->password);
        $attendee->setPassword($hashedPassword);

        return $attendee;
    }
}
