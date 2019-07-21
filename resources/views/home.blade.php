@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">New Exchange</div>
                <div class="card-body">
                {!! Form::open(['url', 'method' => 'post', 'files'=> true, 'id'=>'form']) !!}
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div id="field_wrapper" class="form-group required">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group" id="show_hide_password">
                            <input class="form-control" id="password" type="password" placeholder="Password" required="required">
                            <div class="input-group-append">
                                <button class="btn btn-default reveal" type="button" id="watchPassword"><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" id="addParticipant" class="btn btn-info m-t-2 m-l-2"><i class="fa fa-plus m-1"></i>Add Participant</button>
                        <button type="submit" class="btn btn-success m-t-2"><i class="fa fa-save m-1"></i>Create</button>
                    </div>
                {!! Form::close() !!}    
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal hide" id="pleaseWaitDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Processing...</h5>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped bg-warning progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
            </div>        
        </div>
    </div>  
</div>
<script type="text/javascript">

    $(document).ready(function(){
        var minField = 3;
        var maxField = 12;
        var addButton = $('.add_button');
        var wrapper = $('#field_wrapper');
        var x = 1;
        for (x; x <= minField; x++) { /* Create minimum participants. */
            $(wrapper).append('<div class="table-bordered p-2" style="background-color: rgb(237, 247, 250);"><div class="col-md-6 d-inline-block"><label class="control-label required">Name:</label><input class="form-control" type="text" name="name[]" id="name'+ x +'" placeholder="Participant name" required="required"></div><div class="col-md-6 d-inline-block"><label class="control-label">Cellphone number:</label><input class="form-control" type="number" name="cellphoneNumber[]" id="cellphoneNumber'+ x +'" placeholder="Cellphone number" required="required"></div></div>');
        }
        $(wrapper).on('click', '.remove_participant', function(e){
            e.preventDefault();
            $(this).parent('div').remove();
            x--;
        });
        $('#addParticipant').on('click', function(e) {
            e.preventDefault();
            if (x <= maxField) {
                $(wrapper).append('<div class="table-bordered p-2" style="background-color: rgb(237, 247, 250);"><div class="col-md-6 d-inline-block"><label class="control-label">Name:</label><input class="form-control" type="text" name="name[]" id="name'+ x +'" placeholder="Participant name" required="required"></div><div class="col-md-5 d-inline-block">Cellphone number:<input class="form-control" type="number" name="cellphoneNumber[]" id="cellphoneNumber'+ x +'" placeholder="Cellphone number"></div><a href="javascript:void(0);" class="col-md-1 d-inline-block remove_participant" title="Eliminar"><i class="fa fa-fw fa-trash fa-lg text-danger"></i></a></div>');
                x++;
            }
        });

        $("#watchPassword").on('click', function(e) {
            e.preventDefault();
            if($('#show_hide_password input').attr("type") == "text"){
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass( "fa-eye-slash" );
                $('#show_hide_password i').removeClass( "fa-eye text-info" );
            }else if($('#show_hide_password input').attr("type") == "password"){
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass( "fa-eye-slash" );
                $('#show_hide_password i').addClass( "fa-eye text-info" );
            }
        });

        // Create list.
        let give_to = [];
        let from = [];
        $("#form").on('submit', function(e){
            e.preventDefault();
            $('#pleaseWaitDialog').modal('show');
            random();
            var form = $(this);
            var url = form.attr("action");          
            var data = new FormData(form[0]);
            data.append('password', $('#password').val());
            data.append('qty_participants', (x - 1));
            for (var i = 0; i < from.length; i++) {
                data.append('from[]', from[i]);
                data.append('give_to[]', give_to[i]);
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, xhr) {
                    $('#pleaseWaitDialog').modal('hide');
                    if( xhr.status === 200 ) {
                        location.href = 'http://localhost:8080/SecretFriend/public/home/games'; 
                    }
                }
            });
        });

        function random() {
            var i = 0;
            $("input[name='name[]']").each(function(){
                var $this = $(this);
                from[i] = $this.val();
                i++;
            });
            from = shuffle(from);
            assignment(from);
        }

        function shuffle(array) {
            var currentIndex = array.length, temporaryValue, randomIndex;
            // While there remain elements to shuffle...
            while (0 !== currentIndex) {
                // Pick a remaining element...
                randomIndex = Math.floor(Math.random() * currentIndex);
                currentIndex -= 1;
                // And swap it with the current element.
                temporaryValue = array[currentIndex];
                array[currentIndex] = array[randomIndex];
                array[randomIndex] = temporaryValue;
            }
            return array;
        }

        function assignment(participants) {
            for (var j = 0; j < participants.length; j++) {
                if (participants[j + 1] == null) {
                    give_to[j] = participants[0];
                } else {
                    give_to[j] = participants[j + 1];
                }
            }
        }
    });

</script>
@endsection
