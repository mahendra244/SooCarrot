$.ajaxSetup({
    headers:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function(){
    $('.user-list').click(function(){

        $('#chat-container').html('');

        var getUserId = $(this).attr('data-id');

        receiver_id = getUserId;

        $('.start-head').hide();
        $('.chat-section').show();

        loadOldChats();

    });

    //save chat work

    $('#chat-form').submit(function(e){
        e.preventDefault();

        var message = $('#message').val();

        $.ajax({
            url:"/save-chat",
            type:"POST",
            data: { sender_id:sender_id, receiver_id: receiver_id, message:message },
            success:function(res){

                if(res.success){

                    $('#message').val('');

                    let chat = res.data.message;

                    let html = `
                    <div class="current-user-chat" id='`+res.data.id+`-chat'>
                        <h5>
                            <span>`+chat+`</span>
                            <i class="fa fa-trash" aria-hidden="true" data-id='`+res.data.id+`' data-toggle="modal" data-target="#deleteChatModal"></i>
                            <i class="fa fa-edit" aria-hidden="true" data-id='`+res.data.id+`' data-msg='`+res.data.message+`' data-toggle="modal" data-target="#updateChatModal"></i>
                        </h5>
                    </div>
                    `;

                    $('#chat-container').append(html);
                    scrollChat();

                }
                else{
                    alert(res.msg);
                }

            }
        });

    });

    $(document).on('click','.fa-trash',function(){
        var id = $(this).attr('data-id');
        $('#delete-chat-id').val(id);
        $('#delete-message').text($(this).parent().text());
    });

    $('#delete-chat-form').submit(function(e){
        e.preventDefault();

        var id = $('#delete-chat-id').val();

        $.ajax({
            url:"/delete-chat",
            type:"POST",
            data:{ id:id },
            success:function(res){
                alert(res.msg);
                if(res.success){
                    $('#'+id+'-chat').remove();
                    $('#deleteChatModal').modal('hide');
                }
            }
        });

    });

});//document.ready end

//loadOldChats

function loadOldChats(){

    $.ajax({
        url:"/load-chats",
        type:"POST",
        data:{ sender_id: sender_id, receiver_id: receiver_id },
        success:function(res){

            if(res.success){

                let chats = res.data;
                let html = '';
                for(let i = 0; i < chats.length; i++){
                    let addClass = '';
                    if(chats[i].sender_id == sender_id){
                        addClass = 'current-user-chat';
                    }
                    else{
                        addClass = 'distance-user-chat';
                    }

                    html +=`
                    <div class="`+addClass+`" id='`+chats[i].id+`-chat'>
                        <h5>
                        <span>`+chats[i].message+`</span>`;

                        if(chats[i].sender_id == sender_id){
                            html +=`
                             <i class="fa fa-trash" aria-hidden="true" data-id='`+chats[i].id+`' data-toggle="modal" data-target="#deleteChatModal"></i>
                             <i class="fa fa-edit" aria-hidden="true" data-id='`+chats[i].id+`' data-msg='`+chats[i].message+`' data-toggle="modal" data-target="#updateChatModal"></i>
                             `;
                        }

                    html +=`
                        </h5>
                    </div>
                    `;
                }

                $('#chat-container').append(html);
                scrollChat();

            }
            else{
                alert(res.msg);
            }

        }
    });

}

//scroll div

function scrollChat()
{
    $('#chat-container').animate({
        scrollTop: $('#chat-container').offset().top + $('#chat-container')[0].scrollHeight
    },0);
}

Echo.join('status-update')
.here((users)=>{

    for(let x = 0; x < users.length; x++){
        if(sender_id != users[x]['id']){
            $('#'+users[x]['id']+'-status').removeClass('offline-status');
            $('#'+users[x]['id']+'-status').addClass('online-status');
            $('#'+users[x]['id']+'-status').text('Online');
        }
    }

})
.joining((user)=>{
    $('#'+user.id+'-status').removeClass('offline-status');
    $('#'+user.id+'-status').addClass('online-status');
    $('#'+user.id+'-status').text('Online');
})
.leaving((user)=>{
    $('#'+user.id+'-status').addClass('offline-status');
    $('#'+user.id+'-status').removeClass('online-status');
    $('#'+user.id+'-status').text('Offline');
})
.listen('UserStatusEvent',(e)=>{
    // console.log('hhhh'+e);
});

Echo.private('broadcast-message')
.listen('.getChatMessage', (data) => {
    // console.log(data);
    if(sender_id == data.chat.receiver_id && receiver_id == data.chat.sender_id){

        let html = `
        <div class="distance-user-chat" id='`+data.chat.id+`-chat'>
            <h5><span>`+data.chat.message+`</span></h5>
        </div>
        `;

        $('#chat-container').append(html);
        scrollChat();

    }

});


//delete chat message listen
Echo.private('message-deleted')
.listen('MessageDeletedEvent', (data)=>{
    $('#'+data.id+'-chat').remove();
});


// update chat message

$(document).on('click','.fa-edit',function(){

    $('#update-chat-id').val($(this).attr('data-id'));
    $('#update-message').val($(this).attr('data-msg'));

});

$(document).ready(function(){
    $('#update-chat-form').submit(function(e){
        e.preventDefault();

        var id = $('#update-chat-id').val();
        var msg = $('#update-message').val();

        $.ajax({
            url:"/update-chat",
            type:"POST",
            data:{ id:id, message:msg },
            success:function(res){

                if(res.success){
                    $('#updateChatModal').modal('hide');
                    $('#'+id+'-chat').find('span').text(msg);
                    $('#'+id+'-chat').find('.fa-edit').attr('data-msg',msg);
                }
                else{
                    alert(res.msg);
                }

            }
        });

    });
});

Echo.private('message-updated')
.listen('MessageUpdatedEvent', (data) => {
    // console.log(data)
    $('#'+data.data.id+'-chat').find('span').text(data.data.message);
});


//chat group script

$(document).ready(function(){

    $('#createGroupForm').submit(function(e){
        e.preventDefault();

        $.ajax({
            url:"/create-group",
            type:"POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success:function(res){
                alert(res.msg);
                if(res.success){
                    location.reload();
                }
            }
        });

    });

});//document ready end

function scrollGroupChat()
{
    $('#group-chat-container').animate({
        scrollTop: $('#group-chat-container').offset().top + $('#group-chat-container')[0].scrollHeight
    },0);
}

//member script
$(document).ready(function(){

    $('.addMember').click(function(){

        var id = $(this).attr('data-id');
        var limit = $(this).attr('data-limit');

        $('#add-group-id').val(id);
        $('#add-limit').val(limit);

        $.ajax({
            url:"/get-members",
            type:"POST",
            data:{ group_id:id },
            success:function(res){
                if(res.success){

                    var users = res.data;
                    var html = '';

                    for(let i = 0; i < users.length; i++){
                        let isGroupMemberChecked = '';
                        if(users[i]['group_user'] != null){
                            isGroupMemberChecked = 'checked';
                        }
                        html +=`
                            <tr>
                                <td>
                                    <input type="checkbox" name="members[]" value="`+users[i]['id']+`"
                                    `+isGroupMemberChecked+`/>
                                </td>
                                <td>
                                    `+users[i]['name']+`
                                </td>
                            </tr>
                        `;
                    }

                    $('.addMembersInTable').html(html);

                }
                else{
                    alert(res.msg);
                }
            }
        });

    });

    $('#add-member-form').submit(function(e){
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url:"/add-member",
            type:"POST",
            data: formData,
            success:function(res){
                if(res.success){
                    $('#memberModal').modal('hide');
                    $('#add-member-form')[0].reset();
                    alert(res.msg);
                }
                else{
                    $('#add-member-error').text(res.msg);

                    setTimeout(function(){
                        $('#add-member-error').text('');
                    },3000);
                }
            }
        });

    });

    //delet group chat

    $('.deleteGroup').click(function(){

        $('#delete-group-id').val($(this).attr('data-id'));
        $('#group-name').text($(this).attr('data-name'));

    });

    $('#delete-group-form').submit(function(e){
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url:"/delete-group",
            type:"POST",
            data: formData,
            success: function(res)
            {
                if(res.success){
                    location.reload();
                }
                else{
                    alert(res.msg);
                }
            }
        });

    });

    //update group working script

    $('.updateGroup').click(function(){
        $('#update-group-id').val($(this).attr('data-id'));
        $('#update-group-name').val($(this).attr('data-name'));
        $('#update-group-limit').val($(this).attr('data-limit'));
    });

    $('#updateGroupForm').submit(function(e){
        e.preventDefault();

        $.ajax({
            url:"/update-group",
            type:"POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(res){
                alert(res.msg);
                if(res.success){
                    location.reload();
                }
            }
        });
    });

    $('.copy').click(function(){
        $(this).prepend('<span class="copied_text">Copied</span>');

        setTimeout(()=>{
            $('.copied_text').remove();
        },2000);


        var group_id = $(this).attr('data-id');

        var url = window.location.host+'/share-group/'+group_id;

        var temp = $("<input>");
        $('body').append(temp);
        temp.val(url).select();
        document.execCommand("copy");

        temp.remove();

    });


    //join group script

    $('.join-now').click(function(){

        $(this).text('Wait...');
        $(this).attr('disabled','disabled');

        var group_id = $(this).attr('data-id');

        $.ajax({
            url:"/join-group",
            type:"POST",
            data:{ group_id:group_id },
            success:function(res){
                alert(res.msg);
                if(res.success){
                    location.reload();
                }
                else{
                    $(this).text('Join Now');
                    $(this).removeAttr('disabled');
                }
            }
        })

    });


    //group chat script started

    $('.group-list').click(function(){

        var groupId = $(this).attr('data-id');

        global_group_id = groupId;

        $('#group-chat-container').html('');

        $('.group-start-head').hide();
        $('.group-chat-section').show();

        loadGroupChats();

    });

    //group chat script implemented

    $('#group-chat-form').submit(function(e){
        e.preventDefault();
        // var message = $('#group-message').val();
        var formData = new FormData(this);
        formData.append("sender_id", sender_id);
        formData.append("group_id", global_group_id);
        $.ajax({
            url:"/save-group-chat",
            type:"POST",
            data: formData,
            contentType: false, // Required for file uploads
            processData: false, // Prevent jQuery from processing data
            success:function(res){

                if(res.success){

                    $('#group-message').val('');

                    let chat = res.data.message;
                    let html = `
                        <div class="current-user-chat" id='`+res.data.id+`-chat'>
                            <h5>
                                <span>`+chat+`</span>
                                <i class="fa fa-trash deleteGroupMessage" aria-hidden="true" data-id='`+res.data.id+`' data-toggle="modal" data-target="#groupDeleteChatModal"></i>
                            <i class="fa fa-edit editGroupChat" aria-hidden="true" data-id='`+res.data.id+`' data-msg='`+res.data.message+`' data-toggle="modal" data-target="#updateGroupChatModal"></i>
                            </h5>`;
                            var date = new Date(res.data.user_data.updated_at);
                            var cDate = date.getDate();
                            var cMonth = (date.getMonth()+1) > 9?(date.getMonth()+1):'0'+(date.getMonth()+1);
                            var cYear = date.getFullYear();
                            var cHour = date.getHours();
                            var cMinute = date.getMinutes();
                            var cSecond = date.getSeconds();

                            let cDateTime = cDate+'-'+cMonth+'-'+cYear+' '+(cHour > 9?cHour:'0'+cHour)+'::'+(cMinute > 9?cMinute:'0'+cMinute)+'::'+cSecond;

                            let userImage = (res.data.user_data.image == null)?'/images/dummy.png':res.data.user_data.image;
                            html +=`
                            <div class="user-data">
                                <img src="`+userImage+`" class="user-chat-image"/>
                                    <b>Me</b> ` + formattedDate + `
                            `+cDateTime+`
                            </div>
                        </div>
                    `;

                    $('#group-chat-container').append(html);
                    scrollGroupChat();

                }
                else{
                    alert(res.msg);
                }

            }
        });

    });

});//document.ready

Echo.private('broadcast-group-message')
.listen('.getGroupChatMessage', (data) => {

    if(sender_id != data.chat.sender_id && global_group_id == data.chat.group_id){

        let html = `
        <div class="distance-user-chat" id='`+data.chat.id+`-chat'>
            <h5><span>`+data.chat.message+`</span></h5>`;

        var date = new Date(data.chat.user_data.updated_at);
        var cDate = date.getDate();
        var cMonth = (date.getMonth()+1) > 9?(date.getMonth()+1):'0'+(date.getMonth()+1);
        var cYear = date.getFullYear();
        var cHour = date.getHours();
        var cMinute = date.getMinutes();
        var cSecond = date.getSeconds();

        let cDateTime = cDate+'-'+cMonth+'-'+cYear+' '+(cHour > 9?cHour:'0'+cHour)+'::'+(cMinute > 9?cMinute:'0'+cMinute)+'::'+cSecond;

        let userImage = (data.chat.user_data.image == null)?'/images/dummy.png':data.chat.user_data.image;


        html+=`
            <div class="user-data">
                <img src="`+userImage+`" class="user-chat-image"/>
                    <b>`+data.chat.user_data.name+`</b>
                    `+cDateTime+`
            </div>
        </div>
        `;

        $('#group-chat-container').append(html);
        scrollGroupChat();

    }

});


function loadGroupChats()
{

    $('#group-chat-container').html('');

    $.ajax({
        url:"/load-group-chats",
        type:"POST",
        data:{ group_id: global_group_id },
        success:function(res){
            if(res.success){

                let chats = res.chats;
                let html = '';
                for(let i = 0; i < chats.length; i++){
                    let addClass = 'distance-user-chat';

                    if(chats[i].sender_id == sender_id){
                        addClass = 'current-user-chat';
                    }

                    html += `
                    <div class="`+addClass+`" id='`+chats[i].id+`-chat'>
                        <h5>
                            <span>`+chats[i].message+`</span>`;

                        if(chats[i].sender_id == sender_id){
                            html +=`
                            <i class="fa fa-trash deleteGroupMessage" aria-hidden="true" data-id='`+chats[i].id+`' data-toggle="modal" data-target="#groupDeleteChatModal"></i>
                            <i class="fa fa-edit editGroupChat" aria-hidden="true" data-id='`+chats[i].id+`' data-msg='`+chats[i].message+`' data-toggle="modal" data-target="#updateGroupChatModal"></i>
                            `;
                        }

                        html +=`</h5>`;

                        var date = new Date(chats[i].user_data.updated_at);
                        var cDate = date.getDate();
                        var cMonth = (date.getMonth()+1) > 9?(date.getMonth()+1):'0'+(date.getMonth()+1);
                        var cYear = date.getFullYear();
                        var cHour = date.getHours();
                        var cMinute = date.getMinutes();
                        var cSecond = date.getSeconds();

                        let cDateTime = cDate+'-'+cMonth+'-'+cYear+' '+(cHour > 9?cHour:'0'+cHour)+'::'+(cMinute > 9?cMinute:'0'+cMinute)+'::'+cSecond;

                        let userImage = (chats[i].user_data.image == null)?'/images/dummy.png':chats[i].user_data.image;

                        html +=`
                        <div class="user-data">
                            <img src="`+userImage+`" class="user-chat-image"/>`;

                        if(chats[i].sender_id == sender_id){
                            html +=`
                                <b>Me</b>
                            `;
                        }
                        else{
                            html +=`
                                <b>`+chats[i].user_data.name+`</b>
                            `;
                        }

                        html +=`
                        `+cDateTime+`
                        </div>
                    </div>
                    `;
                }

                $('#group-chat-container').append(html);
                scrollGroupChat();

            }
            else{
                alert(res.msg);
            }
        }
    });

}

//delete group chat script

$(document).ready(function(){

    $(document).on('click','.deleteGroupMessage', function(){
        let msg = $(this).parent().text();
        $('#delete-group-message').text(msg);

        $('#delete-group-message-id').val($(this).attr('data-id'));
    });

    $('#delete-group-chat-form').submit(function(e){
        e.preventDefault();
        var id = $('#delete-group-message-id').val();

        $.ajax({
            url:"/delete-group-chat",
            type:"POST",
            data:{id:id},
            success:function(res){
                if(res.success){
                    $('#'+id+'-chat').remove();
                    $('#groupDeleteChatModal').modal('hide');
                }
                else{
                    alert(res.msg);
                }
            }
        })

    });

});

Echo.private('group-message-deleted')
.listen('GroupMessageDeletedEvent', (data) => {
    $('#'+data.id+'-chat').remove();
});

// update group chat message

$(document).on('click','.editGroupChat',function(){

    $('#update-group-message-id').val($(this).attr('data-id'));
    $('#update-group-message').val($(this).attr('data-msg'));

});

$(document).ready(function(){
    $('#update-group-chat-form').submit(function(e){
        e.preventDefault();

        var id = $('#update-group-message-id').val();
        var msg = $('#update-group-message').val();

        $.ajax({
            url:"/update-group-chat",
            type:"POST",
            data:{ id:id, message:msg },
            success:function(res){

                if(res.success){
                    $('#updateGroupChatModal').modal('hide');
                    $('#'+id+'-chat').find('span').text(msg);
                    $('#'+id+'-chat').find('.fa-edit').attr('data-msg',msg);
                }
                else{
                    alert(res.msg);
                }

            }
        });

    });
});

Echo.private('group-message-updated')
.listen('GroupMessageUpdatedEvent', (data) => {
    // console.log(data)
    $('#'+data.data.id+'-chat').find('span').text(data.data.message);
});
