<x-app-layout>

    <div class="container mt-4">

        <img src="/{{ $groupData->image }}" width="200px" height="200px" alt="">
        <p><b>{{ $groupData->name }}</b></p>
        <p><b>Total Members:- </b> {{ $totalMembers }}</p>

        @if ($available != 0)
            <p>Available for <b>{{ $available }}</b> Members Only!</p>
        @endif

        @if ($isOwner)
            <p>You are the creator of this group, So you can't join this group.</p>
        @elseif ($isJoined > 0)
            <p>You already joined this Group👍👍.</p>
        @elseif ($available == 0)
            <p>Group already filled👎👎.</p>
        @else
            <button class="btn btn-primary join-now" data-id="{{ $groupData->id }}">Join Now</button>
        @endif

    </div>

</x-app-layout>
