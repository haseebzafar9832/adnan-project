@if (session('success'))
    <div class="alert alert-success border-0">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger border-0">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger border-0">
        <strong>Please fix the following.</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
