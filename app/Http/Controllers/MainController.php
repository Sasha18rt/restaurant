<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Weather;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Dish;
use App\Models\DishType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class MainController extends Controller
{   
    public function home()
    {
        if (Auth::check()) {
            $usertype = Auth::user()->usertype;
            if ($usertype == '1') {
                return redirect()->route('admin_users'); 
            }
        }
        $reviews = Review::get(); 
        $menu = Dish::all();
        $dishTypes = DishType::orderBy('order')->get(); 
       
        $reservationText = $this->generateReservationText();
    
        return view('home', [
            'reviews' => $reviews,
            'menu' => $menu,
            'reservationText' => $reservationText,
            'dishTypes' => $dishTypes
        ]);
    }
    
    
    protected function generateReservationText() {
        list($weatherCondition, $temperature) = Weather::getWeather();
        
        if ($weatherCondition !== null && $temperature !== null) {
            $restaurantName = "name";
            
            if ($weatherCondition == 'Clear' && $temperature > 15) {
                return "What a lovely weather! Why not try pizza at {$restaurantName}?";
            } elseif ($weatherCondition == 'Rain') {
                return "It's raining outside. How about some hot soup at {$restaurantName}?";
            } elseif ($weatherCondition == 'Snow') {
                return "Let's warm up with some hot chocolate at {$restaurantName}.";
            } else {
                return "Enjoy your meal at {$restaurantName}.";
            }
        } else {
            return "Failed to retrieve weather information.";
        }
    }

   
    public function navigation()
    {
        return view('navigation-menu');
    }

  

    public function api()
    {
        return view('your.view', ['reservationText' => $reservationText]);
    }

    public function review_check(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|min:4|max:100',
            'subject' => 'required|min:4|max:255',
            'message' => 'required|min:10',
        ]);
    
      
        $user_id = auth()->id();
    
        $review = new Review();
        $review->email = $request->input('email');
        $review->subject = $request->input('subject');
        $review->message = $request->input('message');
        $review->user_id = $user_id; 
        $review->save();
    
        return redirect()->route('review');
    }
    

  

    public function reservation(Request $request) {
        
        if (!Auth::check()) {
            Session::flash('error', 'Please log in to make a reservation!');
            return redirect()->route('home')
    ->with('success', 'Reservation submitted successfully!') 
    ->with('scrollTo', '#reservation'); 


        }
    
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'number' => 'required|integer|min:1|max:20',
            'date' => 'required|date',
            'time' => 'required',
      
        ]);
    
        
        if ($validator->fails()) {
            return redirect()->route('home')->withErrors($validator)->withInput()->with('scrollTo', '#reservation');
        }
        
        
        $dateTime = Carbon::parse($request->date . ' ' . $request->time);
        if ($dateTime->lt(Carbon::now())) {
            return redirect()->route('home')->withErrors($validator)->withInput()->with('scrollTo', '#reservation');
        }
    
      
        $data = new Reservation;
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->guest = $request->number; 
        $data->date = $request->date;
        $data->time = $request->time;
        $data->message = $request->message;
        $data->user_id = Auth::id();
        $data->save();
    
        return redirect()->back()->with('success', 'Reservation submitted successfully!');
    }
    
    public function review()
    {
       
        $reviews = review::get(); 
        return view('review', ['reviews' => $reviews]);
    }
    
    
}
