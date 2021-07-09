<?php

namespace App\Http\Controllers;

use App\Jobs\UserWelcome;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class UserController extends Controller
{
    public function generateJobsandUsers() {
        $users = User::factory()->count(5)->create();
        $jobs = [];
        $luckyUser = null;
        $luckyUserNumber = rand(1,5);
        $count = 0;

        foreach($users as $user) {
            $count++;
            $jobs[] = new UserWelcome($user);
            if($count === $luckyUserNumber){
                $luckyUser = $user;
            }
        }

        if($luckyUser === null) {
            return response('no lucky user', 500);
        }

        Bus::batch($jobs)
        ->name('Welcoming users')
        ->then( function (Batch $batch) use ($luckyUser) { // this is an issue.
            UserWelcome::luckyUserInvitation($luckyUser);
        })
        ->dispatch();

        return response('created');
    }

    public function deleteUsers() {
        User::truncate();
        return response('deleted');
    }
}
