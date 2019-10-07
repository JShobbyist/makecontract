@extends('layouts.admin')

@section('title', 'Contrato')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">contacts</i>
                </div>
                <br>
                <h4 class="card-title">Contrato enviado para {{$user->name}}</h4>
                <a href="{{route('admin.user.contract')}}" class="btn btn-success" type="button">

                    Voltar para a página anterior

                </a>
                <div class="card-content">
                    <br>

                    @if(count($inboxes) > 0)

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">SN</th>
                                    <th class="text-center">Assunto</th>
                                    <th class="text-center">Data</th>
                                    <th class="text-center">Hora</th>
                                    <th class="text-center">Prioridade</th>
                                    <th class="text-center">Ver</th>
                                    <th class="text-center">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $id=0;@endphp
                                @foreach($inboxes as $inbox)
                                    @php $id++;@endphp

                                    <tr>
                                        <td class="text-center">{{ $id }}</td>
                                        <td class="text-center">{{$inbox->title}}</td>
                                        <td class="text-center">{{ date("j/ n/ Y", strtotime($inbox->created_at)) }}</td>
                                        <td class="text-center">{{ date("g:i A", strtotime($inbox->created_at)) }}</td>
                                        <td class="text-center">

                                            @if($inbox->priority == 1)
                                                Normal
                                            @elseif($inbox->priority == 2)
                                                Média
                                            @else
                                                Alta
                                            @endif

                                        </td>
                                        <td class="text-center">

                                            <a href="{{route('userContract.show', $inbox->id)}}" class="btn btn-info"
                                               type="button">

                                                Mostre-me

                                            </a>


                                        </td>

                                        <td class="text-center">

                                            @if($inbox->status == 1)

                                                <button class="btn btn-success">
                                        <span class="btn-label">
                                            <i class="material-icons">check</i>
                                        </span>
                                                    Já Lido
                                                </button>


                                            @else

                                                <button class="btn btn-warning">
                                        <span class="btn-label">
                                            <i class="material-icons">warning</i>
                                        </span>
                                                    Não Lido
                                                </button>



                                            @endif


                                        </td>
                                    </tr>
                                @endforeach


                                </tbody>

                            </table>
                        </div>

                    @else

                        <h1 class="text-center">Contrato para {{$user->name}}</h1>

                    @endif

                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-5">

                            {{$inboxes->render()}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection