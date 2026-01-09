<?php

namespace App\Domain\Auth;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case RECEPTIONIST = 'RECEPTIONIST';
    case PATIENT = 'PATIENT';
}

