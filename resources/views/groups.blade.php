<x-app-layout>

    <div class="container mt-4">
        <h1 style="font-size:30px;">Chatroom</h1>


        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createGroupModal">
            Create Group
        </button>

    <table class="table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Image</th>
                <th>Name</th>
                <th>Limit</th>
                <th>Members</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (count($groups) > 0)
                @php
                    $i = 0;
                @endphp
                @foreach ($groups as $group)

                    <tr>
                        <td>{{ ++$i }}</td>
                        <td><img src="{{ $group->image }}" alt="{{ $group->name }}" width="100px" height="100px"></td>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->join_limit }}</td>
                        <td>
                            <a style="cursor: pointer" class="addMember"
                               data-limit="{{ $group->join_limit }}"
                               data-id="{{ $group->id }}" data-toggle="modal" data-target="#memberModal">
                              Members
                            </a>
                        </td>
                        <td>
                            <i class="fa fa-trash deleteGroup" aria-hidden="true"
                            data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-toggle="modal" data-target="#deleteGroupModal"></i>
                            <i class="fa fa-pencil updateGroup" aria-hidden="true"
                            data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-limit="{{ $group->join_limit }}" data-toggle="modal" data-target="#updateGroupModal"></i>
                            <a class="copy cursor-pointer" data-id="{{ $group->id }}">
                                <i class="fa fa-copy"></i>
                            </a>
                        </td>
                    </tr>

                @endforeach

            @else
                <tr>
                    <th colspan="6">No Groups found!</th>
                </tr>
            @endif
        </tbody>
    </table>


    <!-- Modal -->
    <div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Create chatroom</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form enctype="multipart/form-data" id="createGroupForm">
                <div class="modal-body">

                    <input type="text" name="name" placeholder="Enter Group Name" required class="w-100 mb-2">
                    <input type="file" required name="image" class="w-100 mb-2">
                    <input type="number" name="limit" min="1" placeholder="Enter User Limit" required class="w-100 mb-2">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="memberModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Members</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form id="add-member-form">
                <div class="modal-body">

                    <input type="hidden" name="group_id" id="add-group-id">
                    <input type="hidden" name="limit" id="add-limit">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <div class="addMemberTable">
                                        <table class="table addMembersInTable">

                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <span id="add-member-error"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Members</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Delete Group mODAL-->

    <div class="modal fade" id="deleteGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Leave chatroom</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form id="delete-group-form">
                <div class="modal-body">

                    <input type="hidden" name="id" id="delete-group-id">

                    <p>Are you sure you want to delete <b id="group-name"></b> Group?</p>

                </div>
                <div class="modal-footer">
                    <span id="add-member-error"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete Chatroom</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Update Group Modal -->
    <div class="modal fade" id="updateGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Update Chatroom</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form enctype="multipart/form-data" id="updateGroupForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="update-group-id">
                    <input type="text" name="name" placeholder="Enter Group Name" id="update-group-name" required class="w-100 mb-2">
                    <input type="file" name="image" class="w-100 mb-2">
                    <input type="number" name="limit" min="1" placeholder="Enter User Limit" id="update-group-limit" required class="w-100 mb-2">
                    <p>If you enter the less than limit to previous limit, then we will remove the all members from your group!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    </div>

</x-app-layout>
