<?php

namespace App\Http\Controllers;

use App\Events\UserEnrolled;
use App\Events\UserFinishedFirstThreeModule;
use App\Events\UserFinishedTraining;
use App\Helpers\SectionDurationHelper;
use App\Models\Module;
use App\Models\Section;
use App\Models\Training;
use App\Models\UserProgress;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VolunteerTrainingController extends Controller
{
    public function enroll(Training $training)
    {
        $userEnrolledBefore = $training->users->contains(auth()->user()->id);
        if ($userEnrolledBefore)
        {
            Notification::make()
                ->danger()
                ->title('Enrolled the training')
                ->body('You have already enrolled the training before.')
                ->send();

            UserEnrolled::dispatch(auth()->user(),$training);
            return redirect()->back();
        }

        $training->users()->attach(auth()->user()->id, ['registered_at' => now()]);

        Notification::make()
            ->success()
            ->title('Enrolled the training')
            ->body('You have successfully enrolled the training.')
            ->send();

        return redirect()->route('filament.volunteer.pages.training-detail',['id' => $training->id]);
    }

    public function show(Section $section)
    {
        if (!$section->viewable()) {
            Notification::make()
                ->danger()
                ->title('You are going too fast!')
                ->body('Please complete the previous section.')
                ->send();

            return redirect()->route('filament.volunteer.pages.training-detail', ["id=" . $section->module->training->id]);
        }

        return redirect()->route('filament.volunteer.pages.section-detail',['id'=>$section->id]);
    }

    public function previous(Section $section)
    {
        if ($section->isFirstSection() && !$section->module->isFirstModule())
        {
            $previousSection = Section::find(Module::find($section->module->previousModuleId())->lastSectionId());
        }else
        {
            $previousSection = Section::find($section->previousSectionId());
        }
        return $this->show($previousSection);
    }

    public function next(Section $section)
    {
        if ($section->isLastSection() && !$section->module->isLastModule())
        {
            Notification::make()
                ->info()
                ->title('You are in the next module!')
                ->send();

            $nextSection = Section::find(Module::find($section->module->nextModuleId())->firstSectionId());


            // When User Finished Third Module, Make Him able To Get Interview
            if (str_contains($section->module->title,'Module 3') && auth()->user()->hasRole(['trainee']))
            {
                Notification::make()
                    ->success()
                    ->title('You have completed the third module!')
                    ->body('You can apply for the interview!')
                    ->send();

                UserFinishedFirstThreeModule::dispatch(auth()->user());
                return redirect()->route('filament.volunteer.pages.dashboard');
            }
        }else
        {
            $nextSection = Section::find($section->nextSectionId());
        }
        return $this->show($nextSection);
    }

    public function complete(Section $section)
    {
        $training = $section->module->training;
        $user = auth()->user();


        if (!$training->isCompletedBeforeBy(auth()->user())) {
            $user->trainings()->updateExistingPivot($training->id, ['finished_at' => now()]);

            Notification::make()
                ->success()
                ->title('Training completed!')
                ->body('You have successfully completed the training.')
                ->send();

            UserFinishedTraining::dispatch($user,$training);
        }

        return redirect()->to('/volunteer/trainings');
    }

    public function storeTimerData(Request $request, Section $section)
    {
        $duration = $request->input('timerValue');
        SectionDurationHelper::saveDuration($section,$duration);

        return response()->json(['success' => true]);
    }
}
