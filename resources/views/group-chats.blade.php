<x-app-layout>

    <div class="container mt-4">

        <div class="row">

            @if(count($groups) > 0 || count($joinedGroups) > 0)

                <div class="col-md-3">

                    <ul class="list-group">
                        @foreach ($groups as $group)

                            <li class="list-group-item list-group-item-dark cursor-pointer group-list" data-id="{{ $group->id }}">
                                <img src="/{{ $group->image }}" alt="" class="user-image">
                                {{ $group->name }}
                            </li>

                        @endforeach

                        <!-- Joined Groups Show -->

                        @foreach ($joinedGroups as $group)

                            <li class="list-group-item list-group-item-dark cursor-pointer group-list" data-id="{{ $group->getGroup->id }}">
                                <img src="/{{ $group->getGroup->image }}" alt="" class="user-image">
                                {{ $group->getGroup->name }}
                            </li>

                        @endforeach

                    </ul>

                </div>

                <div class="col-md-9">

                    <h3 class="group-start-head">Click to start the chat</h3>

                    <div class="group-chat-section">

                        <div id="group-chat-container">

                        </div>

                        <!-- <form action="" id="group-chat-form">
                            <input type="text" name="message" placeholder="Enter Message" id="group-message" class="border" required>
                            <input type="submit" value="Send Message" class="btn btn-primary">
                        </form> -->
                        <form action="" id="group-chat-form" enctype="multipart/form-data">
                            <span class="emoji-icon">😊</span>
                            <input type="text" name="message" placeholder="Enter Message" id="group-message" class="border" required>
                            <label for="file-upload" class="attachment-icon">📎</label>
                            <input type="file" name="attachment"  id="file-upload" style="display: none;">
                            <input type="submit" value="Send Message" class="btn btn-primary">
                            
                        </form>


                    </div>

                </div>

            @else

                <div class="col-md-12">
                    <h6>Groups not Found!</h6>
                </div>

            @endif

        </div>

    </div>

     <!-- Modal -->
  <div class="modal fade" id="groupDeleteChatModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Delete Chat</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form action="" id="delete-group-chat-form">
            <div class="modal-body">
                <input type="hidden" name="id" id="delete-group-message-id">
                <p>Are you sure you want to delete below Message?</p>
                <p><b id="delete-group-message"></b></p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Update chat Modal -->
  <div class="modal fade" id="updateGroupChatModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Edit Chat</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form action="" id="update-group-chat-form">
            <div class="modal-body">
                <input type="hidden" name="id" id="update-group-message-id">
                <input type="text" name="message" class="form-control" placeholder="Enter Message" required id="update-group-message">
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>

      </div>
    </div>
  </div>

</x-app-layout>
