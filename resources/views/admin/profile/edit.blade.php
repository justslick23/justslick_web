@extends('layouts.admin')

@section('title', 'Profile & Contact')

@section('content')
    <h1 class="h3 mb-4">Profile & Contact</h1>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-2">Please correct the following:</p>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="admin-card">
                    <h2 class="h5 mb-4">Artist details</h2>

                    @php
                        $fields = [
                            'artist_name' => ['Artist name', 100],
                            'tagline' => ['Tagline', 150],
                            'location' => ['Location', 100],
                            'award' => ['Award or achievement', 200],
                        ];
                    @endphp

                    @foreach ($fields as $name => [$label, $limit])
                        <div class="mb-3">
                            <label for="{{ $name }}" class="form-label">
                                {{ $label }}
                            </label>

                            <input id="{{ $name }}" name="{{ $name }}"
                                   type="text" maxlength="{{ $limit }}"
                                   value="{{ old($name, $profile->{$name}) }}"
                                   class="form-control @error($name) is-invalid @enderror"
                                   @required($name === 'artist_name')
                                   @error($name)
                                       aria-invalid="true"
                                       aria-describedby="{{ $name }}-error"
                                   @enderror>

                            @error($name)
                                <div id="{{ $name }}-error" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endforeach

                    <div class="mb-4">
                        <label for="short_bio" class="form-label">
                            Short biography
                        </label>

                        <textarea id="short_bio" name="short_bio"
                                  rows="3" maxlength="500"
                                  class="form-control @error('short_bio') is-invalid @enderror"
                                  aria-describedby="short-bio-help @error('short_bio') short-bio-error @enderror">{{ old('short_bio', $profile->short_bio) }}</textarea>

                        @error('short_bio')
                            <div id="short-bio-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div id="short-bio-help" class="form-text">
                            A short introduction for the homepage. Maximum 500 characters.
                        </div>
                    </div>

                    <div>
                        <label for="biography" class="form-label">
                            Full biography
                        </label>

                        <textarea id="biography" name="biography"
                                  rows="10" maxlength="10000"
                                  class="form-control @error('biography') is-invalid @enderror"
                                  aria-describedby="biography-help @error('biography') biography-error @enderror">{{ old('biography', $profile->biography) }}</textarea>

                        @error('biography')
                            <div id="biography-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div id="biography-help" class="form-text">
                            Plain text for your About page and press kit.
                            Separate paragraphs with a blank line.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-card mb-4">
                    <h2 class="h5 mb-3">Contact</h2>

                    <label for="booking_email" class="form-label">
                        Booking email
                    </label>

                    <input id="booking_email" name="booking_email"
                           type="email" maxlength="254" required
                           value="{{ old('booking_email', $profile->booking_email) }}"
                           class="form-control @error('booking_email') is-invalid @enderror"
                           aria-describedby="booking-help @error('booking_email') booking-error @enderror">

                    @error('booking_email')
                        <div id="booking-error" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="booking-help" class="form-text">
                        Public contact email. Changing this does not change
                        your admin login email.
                    </div>
                </div>

                <div class="admin-card">
                    <h2 class="h5 mb-3">Social & streaming profiles</h2>

                    <p class="small text-body-secondary">
                        Use full HTTPS links. Leave a field blank to hide it
                        when we connect the public website.
                    </p>

                    @foreach ($platforms as $key => $label)
                        @php($field = "social_links.$key")

                        <div class="mb-3">
                            <label for="social-{{ $key }}" class="form-label">
                                {{ $label }}
                            </label>

                            <input id="social-{{ $key }}"
                                   name="social_links[{{ $key }}]"
                                   type="url" maxlength="500"
                                   placeholder="https://"
                                   value="{{ old($field, $profile->social_links[$key] ?? '') }}"
                                   class="form-control @error($field) is-invalid @enderror"
                                   @error($field)
                                       aria-invalid="true"
                                       aria-describedby="social-{{ $key }}-error"
                                   @enderror>

                            @error($field)
                                <div id="social-{{ $key }}-error"
                                     class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-brand">
                Save profile
            </button>
        </div>
    </form>
@endsection