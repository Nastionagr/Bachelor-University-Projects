@extends('layout.page')

@section('content')

<main class="content container my-4">
    <div class="row flex-fill py-md-0 py-5">
        <h1>Survey</h1>
        <hr class="my-2" />
        @foreach ($questions as $question)
        <div class="row mx-2 py-2 pb-3 pb-sm-0 border-bottom">
            <h6>{{ $question['question_text'] }}</h6>
        </div>
        @endforeach
    </div>
</main>
@endsection