<?php

namespace App\Domain\Service;

use App\Application\Request\AttendeeRequest;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Product;

class AttendeeDomainService
{
    public function createAttendee(AttendeeRequest $attendeeRequest, Product $product): Attendee
    {
        return new Attendee(
            name: $attendeeRequest->name,
            firstName: $attendeeRequest->firstName,
            middleName: $attendeeRequest->middleName,
            familyName: $attendeeRequest->familyName,
            nickName: $attendeeRequest->nickName,
            email: $attendeeRequest->email,
            orderCode: $attendeeRequest->orderCode,
            ticketId: $attendeeRequest->ticketId,
            ticketSecret: $attendeeRequest->ticketSecret,
            product: $product
        );
    }
}
