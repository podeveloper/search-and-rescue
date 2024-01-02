<?php

namespace App\Helpers;

use App\Models\User;
use Filament\Notifications\Notification;

class RoleHelper
{
    public static function moveTo(User $record, $role)
    {
        $record->roles()->detach();
        $record->assignRole($role);

        Notification::make()
            ->success()
            ->title('User Moved')
            ->body('The user have been moved to the "'.$role.'" role.')
            ->send();
    }
}
