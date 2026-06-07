<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class RfidLogin extends Page
{
    protected string $view = 'filament.pages.rfid-login';

    // This keeps the page hidden from the sidebar menu
    protected static bool $shouldRegisterNavigation = false;

    public $rfid_input = '';
    public $pending_employee_id = null;

    /**
     * This function runs automatically every time $rfid_input changes
     */
    public function updatedRfidInput()
    {
        $employee = Employee::where('rfid_uid', $this->rfid_input)
            ->where('is_active', true)
            ->first();

        if ($employee) {
            // Store employee temporarily and wait for face verification
            $this->pending_employee_id = $employee->id;
            
            // Dispatch browser event to tell Alpine.js to capture the face
            $this->dispatch('request-face-scan');
        } else {
            // If no match, clear the input for the next scan
            $this->reset('rfid_input');
            
            Notification::make()
                ->title('Access Denied')
                ->body('Invalid RFID Card.')
                ->danger()
                ->send();
        }
    }

    public function verifyFaceMatch($capturedDescriptorJson)
   {
        if (!$this->pending_employee_id) {
            return;
        }

        $employee = Employee::find($this->pending_employee_id);
        
        if (!$employee || !$employee->face_descriptor) {
            $this->reset(['rfid_input', 'pending_employee_id']);
            Notification::make()
                ->title('Access Denied')
                ->body('Facial data not found for this employee. Please register your face first.')
                ->danger()
                ->send();
            return;
        }

        $capturedDescriptor = json_decode($capturedDescriptorJson, true);
        $storedDescriptor = json_decode($employee->face_descriptor, true);

        if (!is_array($capturedDescriptor) || !is_array($storedDescriptor)) {
            $this->reset(['rfid_input', 'pending_employee_id']);
            Notification::make()
                ->title('Error')
                ->body('Invalid face data format.')
                ->danger()
                ->send();
            return;
        }

        $distance = $this->calculateEuclideanDistance($capturedDescriptor, $storedDescriptor);

        // face-api.js default threshold for Euclidean distance is 0.6. 
        // 0.5 is slightly stricter for better security.
        if ($distance !== false && $distance <= 0.5) {
            // Match successful!
            Auth::login($employee);
            
            Notification::make()
                ->title('Access Granted')
                ->body('Face verified successfully.')
                ->success()
                ->send();

            return redirect()->to('/admin');
        } else {
            // Match failed
            $this->reset(['rfid_input', 'pending_employee_id']);
            
            Notification::make()
                ->title('Access Denied')
                ->body('Face verification failed. Faces do not match.')
                ->danger()
                ->send();
                
            // Tell frontend to resume listening for RFID
            $this->dispatch('resume-rfid-scan');
        }
    }

    private function calculateEuclideanDistance(array $a, array $b)
    {
        if (count($a) !== count($b)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }

        return sqrt($sum);
    }
}