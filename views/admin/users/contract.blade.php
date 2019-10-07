@extends('layouts.admin')

@section('title', 'Contrato')

@section('content')
    <style type="text/css">
        #file{
            visibility: visible;
        }
    </style>

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">contacts</i>
                </div>
                <br>
                <h4 class="card-title">Contrato</h4>
                <div class="card-content">
                    <br>
                    <div class="table-responsive">

                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Nome</th>
                                <th class="text-center">Principal</th>
                                <th class="text-center">REferência</th>
                                <th class="text-center">Depósito</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Modelo<br>Editar</th>
                                <th class="text-center">Contrato</th>
                                <th class="text-center">Visão</th>
                            </tr>
                            </thead>
                            <tbody>

                            @if($users)

                                @php $id=0;@endphp

                                @foreach($users as $user)

                                    @php $id++;@endphp

                                    <tr>
                                        <td class="text-center">{{ $id }}</td>
                                        <td width="10%">
                                            <img src="{{asset($user->profile->avatar)}}" class="img-circle"
                                                 alt="User Photo">
                                        </td>
                                        <td class="text-center">{{$user->name}}</td>
                                        <td class="text-center">{{config('app.currency_symbol')}} {{$user->profile->main_balance + 0}}</td>
                                        <td class="text-center">{{config('app.currency_symbol')}} {{$user->profile->referral_balance +0}}</td>
                                        <td class="text-center">{{config('app.currency_symbol')}} {{$user->profile->deposit_balance +0}}</td>
                                        <td class="text-center">
                                            @if($user->active == 0)
                                                Desabilitado
                                            @else
                                                Ativo
                                            @endif
                                        </td>
                                        <td class="td-actions text-center">
                                            <a href="{{route('admin.user.contractedit')}}" type="button"
                                               rel="tooltip" class="btn btn-primary">
                                                <i class="material-icons">edit</i>
                                            </a>
                                        </td>
                                        <td class="td-actions text-center">
                                            <form action="{{route('admin.user.upcontract', $user->id)}}" role="form" id="contact-form" method="POST"
                          enctype="multipart/form-data">
                                                {{csrf_field()}}
                                                <input type="hidden" name="email" value="{{$user->email}}">
                                                <div class="form-data">
                                                    <div class="form-control">
                                                        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                                            <div>
                                                                <span class="btn btn-rose btn-round btn-file">
                                                                    <span class="fileinput-new">Selecionar Arquivo</span>
                                                                    <span class="fileinput-exists">Alterar</span>
                                                                    <input type="file" name="featured"/>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-control">
                                                            <button type="submit" class="btn btn-primary pull-middle">Enviar Contrato</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="td-actions text-center">
                                            <a href="{{route('admin.user.viewcontract', $user->id)}}" type="button"
                                               rel="tooltip" class="btn btn-danger">
                                               <i class="material-icons">visibility</i>
                                           </a>
                                        </td>
                                    </tr>
                                @endforeach

                            @endif

                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-5">

                            {{$users->appends(['s'=>$s])->render()}}

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3">
                <div class="card card-content">
                    <div class="card-content">
                        <form action="{{route('admin.users.index')}}" method="get">
                            <div class="form-group label-floating">
                                <label for="s" class="control-label">Pesquisar</label>
                                <input type="text" id="s" name="s" value="{{isset($s) ? $s : ''}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary ">Pesquisar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
