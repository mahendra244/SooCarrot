<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Models\GroupChat;
use App\Models\Group;
use App\Models\Member;
use App\Events\MessageEvent;
use App\Events\GroupMessageEvent;
use App\Events\GroupMessageDeletedEvent;
use App\Events\MessageDeletedEvent;
use App\Events\MessageUpdatedEvent;
use App\Events\GroupMessageUpdatedEvent;

class UserController extends Controller
{
    //
    public function loadDashboard()
    {
        $users = User::whereNotIn('id', [ auth()->user()->id ])->get();
        return view('dashboard', compact('users'));
    }

    public function saveChat(Request $request)
    {
        try {

            $chat = Chat::create([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message
            ]);

            event(new MessageEvent($chat));

            return response()->json([ 'success' => true,'data' => $chat  ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

    public function loadChats(Request $request)
    {
        try {

            $chats = Chat::where(function($query) use ($request){
                $query->where('sender_id','=', $request->sender_id)
                    ->orWhere('sender_id','=', $request->receiver_id);
            })->where(function($query) use($request){
                $query->where('receiver_id','=', $request->sender_id)
                    ->orWhere('receiver_id','=', $request->receiver_id);
            })->get();

            return response()->json([ 'success' => true,'data' => $chats  ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

    public function deleteChat(Request $request)
    {
        try {

            Chat::where('id', $request->id)->delete();

            event(new MessageDeletedEvent($request->id));

            return response()->json([ 'success' => true,'msg' => 'Chat Deleted Successfully!'  ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

    public function updateChat(Request $request)
    {
        try {

            Chat::where('id', $request->id)->update([ 'message' => $request->message ]);

            $chat = Chat::where('id', $request->id)->first();

            event(new MessageUpdatedEvent($chat));

            return response()->json([ 'success' => true,'msg' => 'Chat Updated Successfully!'  ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

    public function loadGroups()
    {
        $groups = Group::where('creator_id', auth()->user()->id)->get();
        return view('groups',compact('groups'));
    }

    public function createGroup(Request $request)
    {
        try{

            $imageName = '';
            if($request->image){
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                $imageName = 'images/'.$imageName;
            }

            Group::insert([

                'creator_id' => auth()->user()->id,
                'name' => $request->name,
                'image' => $imageName,
                'join_limit' => $request->limit
            ]);

            return response()->json([ 'success' => true, 'msg' => $request->name.' Group has been created Successfully!' ]);

        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);
        }
    }

    public function getMembers(Request $request)
    {
        try{

            $users = User::with(['groupUser' => function($query) use($request){
                $query->where('group_id',$request->group_id);
            }])
            ->whereNotIn('id',[auth()->user()->id])->get();

            return response()->json([ 'success' => true, 'data' => $users]);

        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);
        }
    }

    public function addMembers(Request $request)
    {
        try{

            if(!isset($request->members)){
                return response()->json([ 'success' => false, 'msg' => 'Please select any one Member!']);
            }
            else if(count($request->members) > (int)$request->limit){
                return response()->json([ 'success' => false, 'msg' => 'You can not select more than '.$request->limit.' Members!']);
            }
            else{

                Member::where('group_id',$request->group_id)->delete();

                $data = [];
                $x = 0;
                foreach($request->members as $user){
                    $data[$x] = ['group_id'=>$request->group_id, 'user_id' => $user];
                    $x++;
                }

                Member::insert($data);

                return response()->json([ 'success' => true, 'msg' => 'Members added Successfully!']);

            }

        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);
        }
    }

    public function deleteGroup(Request $request)
    {
        try{

            Group::where('id',$request->id)->delete();
            Member::where('group_id',$request->id)->delete();

            return response()->json([ 'success' => true]);

        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);
        }
    }

    public function updateGroup(Request $request)
    {
        try{

            $data = [];

            $imageName = '';
            if($request->image){
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                $imageName = 'images/'.$imageName;

                $data = [
                    'creator_id' => auth()->user()->id,
                    'name' => $request->name,
                    'join_limit' => $request->limit,
                    'image' => $imageName
                ];

            }
            else{

                $data = [
                    'creator_id' => auth()->user()->id,
                    'name' => $request->name,
                    'join_limit' => $request->limit
                ];

            }

            $groupData = Group::where('id',$request->id)->first();

            if($request->limit < $groupData->join_limit){
                Member::where('group_id',$request->id)->delete();
            }

            Group::where('id',$request->id)->update($data);

            return response()->json([ 'success' => true, 'msg'=>'Group has been updated Successfully!']);

        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);
        }
    }

    public function shareGroup($id)
    {
        $groupData = Group::where('id',$id)->first();

        if($groupData){
            $totalMembers = Member::where('group_id',$id)->count();

            $available = $groupData->join_limit - $totalMembers;

            $isOwner = $groupData->creator_id == auth()->user()->id ? true: false;

            $isJoined = Member::where(['group_id' => $id, 'user_id' => auth()->user()->id])->count();

            return view('shareGroup',compact([ 'groupData','totalMembers','available', 'isOwner', 'isJoined' ]));

        }
        else{
            return view('404');
        }

    }

    public function joinGroup(Request $request)
    {
        try{

            Member::insert([
                'group_id' => $request->group_id,
                'user_id' => auth()->user()->id
            ]);

            return response()->json([ 'success' => true, 'msg' => 'Congratulations, You have been joined Successfully!' ]);
        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);
        }
    }

    public function groupChats()
    {
        $groups = Group::where('creator_id', auth()->user()->id)->get();
        $joinedGroups = Member::with('getGroup')->where('user_id', auth()->user()->id)->get();
        return view('group-chats', compact(['groups','joinedGroups']));
    }

    public function saveGroupChat(Request $request)
    {
        try {
            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment'); // Assign the file to $attachment
                $extension = $attachment->getClientOriginalExtension(); // Get the file extension
            
                $directory = $extension === 'mp4' ? 'root/video' : 'root/picture';
                $attachmentPath = $attachment->store($directory, 'local'); // Store the file
            }
            
    

            $chat = GroupChat::create([
                'sender_id' => $request->sender_id,
                'group_id' => $request->group_id,
                'message' => $request->message
            ]);
            // Handle attachment
          

            $chat = GroupChat::with('userData')->where('id',$chat->id)->first();


            event(new  ($chat));

            return response()->json([ 'success' => true,'data' => $chat  ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

    public function loadGroupChats(Request $request)
    {
        try{

            $chats = GroupChat::with('userData')->where('group_id', $request->group_id)->get();

            return response()->json([ 'success' => true, 'chats' => $chats ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

    public function deleteGroupChat(Request $request)
    {
        try{

            $chats = GroupChat::where('id', $request->id)->delete();

            event(new GroupMessageDeletedEvent($request->id));

            return response()->json([ 'success' => true, 'msg' => 'Group Chat Message Deleted Successfully!' ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

    public function updateGroupChat(Request $request)
    {
        try {

            GroupChat::where('id', $request->id)->update([ 'message' => $request->message ]);

            $chat = GroupChat::where('id', $request->id)->first();

            event(new GroupMessageUpdatedEvent($chat));

            return response()->json([ 'success' => true,'msg' => 'Chat Updated Successfully!'  ]);

        } catch (\Exception $e) {

            return response()->json([ 'success' => false, 'msg' => $e->getMessage() ]);

        }
    }

}
