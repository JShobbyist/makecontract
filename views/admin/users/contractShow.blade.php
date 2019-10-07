@extends('layouts.admin')

@section('title', 'Contrato')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <h2 class="title"> Ver Contrato do Usuário </h2>
                        <h5 class="description"> Prioridade da Mensagem : @if($inbox->priority == 1)
                                Normal
                            @elseif($inbox->priority == 2)
                                Média
                            @else
                                Alta
                            @endif</h5>
                    </div>
                </div>
                <div class="card-content">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="card card-pricing card-raised">
                            <div class="card-content">
                                <div class="icon icon-info">
                                    <i class="material-icons">mail_outline</i>
                                </div>
                                <h5 class="card-title">
                                    <b> Assunto da Mensagem: {{$inbox->title}}</b>
                                </h5>
                                <br>
                                <div class="card-title">

                                    {!! $inbox->body !!}
                                </div>
                                <a href="{{route('admin.user.viewcontract', $user->id)}}" class="btn btn-success pull-left" type="button">

                                    Voltar ao Contrato

                                </a>
                                @if(!is_null($inbox->file))
                                    <a href="{{asset($inbox->file)}}" class="btn btn-warning pull-middle" type="button">

                                        Visualizar Anexo

                                    </a>

                                    <a href="{{route('userMessage.download',$inbox->id)}}" class="btn btn-info pull-right" type="button">

                                        Download do Anexo

                                    </a>
                                  @else

                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>




@endsection