<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;

class MyDriverProfile extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): ?string
    {
        return 'fas-id-card';
    }

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 2;

    public $compositeImage;

    public function __construct()
    {
        $this->compositeImage = self::compositeImage();
    }

    public static function getNavigationLabel(): string
    {
        return __('general.edit_driver_profile');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.edit_driver_profile');
    }

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return __('general.edit_driver_profile');
    }

    public ?array $driverProfileData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editDriverProfileForm',
        ];
    }

    public function editDriverProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                CheckboxList::make('drivingEquipments')
                    ->relationship(titleAttribute: 'name')
                    ->disabled()
                ->label('')
            ])
            ->model($this->getUser())
            ->statePath('driverProfileData');
    }

    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (!$user instanceof Model) {
            throw new \Exception('The authenticated user object must be instance of User Model');
        }

        return $user;
    }

    protected function fillForms(): void
    {
        $user = $this->getUser();

        $userData = $user->attributesToArray();

        $this->editDriverProfileForm->fill($userData);

    }

    protected function getUpdateDriverProfileFormActions(): array
    {
        return [
            //Action::make('updateDriverProfileAction')
            //    ->requiresConfirmation()
            //    ->label('Save')
            //    ->submit('editDriverProfileForm'),
        ];
    }

    public function updateDriverProfile(): void
    {
        try {
            $data = $this->editDriverProfileForm->getState();
            dd($data);

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }
        $this->sendSuccessNotification();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        //

        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title('Driver Profile Updated')
            ->send();
    }

    public static function compositeImage()
    {
        $user = auth()->user();

        // List of driving equipment items with positions
        $drivingEquipmentItems = [
            //"INTERCOM" => ['file_name' => 'intercom.png','position' => 'top-center', 'x' => 20, 'y' => 20],
            "SÜRÜŞ KASKI" => ['file_name' => 'helmet-removebg-preview.png','position' => 'top-center', 'x' => 0, 'y' => 0],
            "SÜRÜŞ DİRSEKLİĞİ" => ['file_name' => 'dirsek-removebg-preview.png','position' => 'top-center', 'x' => 0, 'y' => 90],
            "SÜRÜŞ CEKETİ" => ['file_name' => 'jacket-removebg-preview.png','position' => 'center', 'x' => 0, 'y' => -100],
            "SÜRÜŞ ELDİVENİ" => ['file_name' => 'gloves-removebg-preview.png','position' => 'center', 'x' => 0, 'y' => 15],
            "SÜRÜŞ PANTOLONU" => ['file_name' => 'trouser-removebg-preview.png','position' => 'center', 'x' => -5, 'y' => 5],
            "SÜRÜŞ DİZLİĞİ" => ['file_name' => 'knee-removebg-preview.png','position' => 'center', 'x' => 0, 'y' => 100],
            "SÜRÜŞ BOTU" => ['file_name' => 'boot-removebg-preview.png','position' => 'center', 'x' => 0, 'y' => 220],
        ];

        // Example using Intervention Image
        $background = Image::make(public_path('img/driver-profile/body.png'));

        // Example equipment images (replace with actual images)
        foreach ($drivingEquipmentItems as $name => $item) {
            // Check if the user has the current equipment
            $hasEquipment = $user->drivingEquipments()->where('name', $name)->exists();

            // Insert the corresponding image onto the composite image if the user has the equipment
            if ($hasEquipment) {
                $imagePath = public_path('img/driver-profile/' . $item['file_name']);
                $equipmentImage = Image::make($imagePath);

                // Adjust positions based on the defined positions in $drivingEquipmentItems
                $background->insert($equipmentImage, $item['position'], $item['x'], $item['y']);
            }
        }

        // Get the composed image as a data URL
        return $background->encode('data-url')->encoded;
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.candidate.pages.my-driver-profile';
}
