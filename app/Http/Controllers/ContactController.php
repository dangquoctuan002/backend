<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Repositories\ContactRepository;

class ContactController extends Controller
{
    protected $contactRepository;
    public function __construct(ContactRepository $contactRepository){
        $this->contactRepository = $contactRepository;
    }
    public function index(Request $request)
    {
        $contact = $this->contactRepository->listWhere([
            "page"=>$request->get('page',1),
            "limit"=>9]
        );
        return response()->json($contact);
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json(['status' => 1, 'data' => $contact]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'message' => 'required|string',
        ]);

        Contact::create($validated);

        return response()->json(['message' => 'Contact saved successfully'], 200);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $relatedDataCount = $contact->products()->count();
    
        if ($relatedDataCount > 0) {
            return response()->json(['status' => 0, 'message' => 'This contact cannot be deleted because there is associated data'], 400);
        }
    
        $contact = $this->contactRepository->deleteWhere($id);
    
        return response()->json(['status' => 1, 'message' => 'The contact has been successfully deleted']);
    }
}

