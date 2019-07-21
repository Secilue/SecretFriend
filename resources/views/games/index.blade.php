@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">My games</div>
                <div class="card-body">
                @if (isset($games))
                    <div class="panel-group" id="accordion">
                    @foreach($games as $index => $game)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                            <a data-toggle="collapse" id="{{ $game->id }}" data-parent="#accordion" href="{{ '#collapse'.$game->id }}">{{ 'Game No#'.$game->id.'. '.$game->qty_participants.' participants.' }}</a>
                            </h4>
                        </div>
                        <div id="{{ 'collapse'.$game->id }}" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="game_data" class="table table-striped table-bordered table-hover text-center">
                                        <thead>
                                            <tr>
                                                <th>From</th>
                                                <th>To</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($game->gameParticipants as $key => $participant)
                                                <tr>
                                                    <td>{{ $participant->name }}</td>
                                                    @if($game->locked)
                                                        <td>It's secret</td>
                                                    @else
                                                        @foreach($game->gameParticipants as $k => $from)
                                                            @if($participant->pivot->give_to == $from->id)
                                                                <td>{{ $from->name }}</td>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($game->locked)
                                        <div class="text-center">
                                            <button type="button" id="unlock" value="{{ $game->id }}" class="btn btn-warning m-t-2 m-l-2 unlock"><i class="fa fa-unlock m-1"></i>Unlock Game</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                @else
                    <div>Have not games, create one, go!</div>
                @endif  
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade hide" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Unlock list</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        If you unlock the list, each participant will be informed that the list is not secret.
        <div class="form-group mt-5">
            <div class="input-group" id="show_hide_password">
                <input class="form-control" id="password" type="password" placeholder="Password">
                <div class="input-group-append">
                    <button class="btn btn-default reveal" type="button" id="watchPassword"><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                </div>
            </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="getParticipantsTo" class="btn btn-danger" disabled>Accept</button>
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
<!-- The Modal -->
<div class="modal fade hide" id="errorPassword">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title">Password incorrect, please try again.</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">

    $(document).ready(function(){
        var actual_id;
        $('.unlock').on('click', function() {
            actual_id = $(this).val();
            $('#exampleModal').modal('show');
        });

        function unlock() {
            actual_id = $(this).val();
            $('#exampleModal').modal('show');
        }

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

        $('#password').bind('keyup mouseup', function() {
            var value = $('#password').val();
            if (value.length > 0) {
                $('#getParticipantsTo').removeAttr("disabled");
            } else {
                $("#getParticipantsTo").attr("disabled", true);
            }
        });

        $('#getParticipantsTo').on('click', function(e) {
            e.preventDefault();
            $('#exampleModal').modal('hide');
            $('#pleaseWaitDialog').modal('show'); 
            var password = $('#password').val();
            $.ajax({
                url: 'games/' + actual_id,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'password' : password, _token: '{{ csrf_token() }}'},
                success: function(data, textStatus, xhr) {
                    $('#pleaseWaitDialog').modal('hide');
                    if( xhr.status === 200 ) {
                        location.reload(); 
                    } else if ( xhr.status === 201 ) {
                        $('#errorPassword').modal('show');
                    }
                }
            });
        });
    });
</script>
@endsection