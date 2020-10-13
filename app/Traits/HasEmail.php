<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait HasEmail
{
    public function scopeWhereEmail($query, $email)
    {
        $email = preg_quote($email);
        return $query->where('email', 'ilike', mb_strtolower($email));
    }

    public function scopeWhereEmailsIn($query, $emails)
    {
        return $query->where(function ($query) use ($emails) {

            foreach ($emails as $email) {
                $email = preg_quote($email);
                $email = mb_strtolower($email);
                $query->orWhere('email', 'ilike', $email);
            }
        });
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = mb_strtolower($value);
    }

    public function getEmailAttribute($value)
    {
        return mb_strtolower($value);
    }

    public function isValid(): bool
    {
        $validator = Validator::make([
            'email' => $this->email
        ], [
            'email' => 'email:rfc',
        ]);

        if ($validator->fails()) {
            return false;
        } else {
            return true;
        }
    }

    public function isValidRefresh()
    {
        $this->is_valid = $this->isValid();
    }
}
