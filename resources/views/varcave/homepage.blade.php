@include('varcave.template.header')
@include('varcave.template.navbar')

<script src="/varcave/homepage.js"></script>
<section class="section">
    <div class="container">
      <h1 class="title is-2">
        {{ __('varcave.homepage.hometext') }}
      </h1>
      <p class="subtitle">
        {{ $settings->get('legal_notice') }}
      </p>
      <p class="subtitle">
        {{ __('varcave.homepage.current_cave_count', ['count' => $caveCount]) }}
      </p>
      <p>
        @guest
          {{ __('varcave.homepage.connectinfo') }}
        @endguest
      </p>
    </div>
</section>

<section class="section">
  <div class="container">
    <div class="columns is-5">
      <div class="column ml-2 mr-2">
          <h3 class="title is-3">{{ __('varcave.homepage.homeAnnouncements') }}</h3>
          @foreach($homeAnnouncements as $announce)

            <article id="announcement-{{$announce->id}}" class="message">
              <div class="message-header">
                <p>{{$announce->title}}</p>
                <button class="card-header-icon" aria-label="more options">
                  <span class="icon">
                    <i class="bi bi-chevron-up"></i>
                  </span>
                </button>
              </div>
              <div class="message-body">
                {!! $announce->content !!}
              </div>
            </article>

          @endforeach
      </div>

      <div class="column">
        <h3 class="title is-3">{{ __('varcave.homepage.lastCavesUpdates')}}</h2>
        @foreach($caveChangelogs as $cavelog)
            <div class="card is-link">
              <div class="card-content">
                <div class="media">
                  <div class="media-left">
                    <figure class="image is-48x48">
                      <img
                        src="{{ asset('img/logo_cavite_150x150.png') }}"
                        alt="small cave logo"
                      />
                    </figure>
                  </div>
                  <a href="{{route('varcave.caves.show', $cavelog->cave->uuid)}}" target="_blank"><div class="media-content ">
                    <p class="title is-5 has-text-weight-semibold">{{ $cavelog->cave->name }}</p>
                    <p class="subtitle is-7">{{$cavelog->created_at}}</p>
                  </div></a>
                </div>

                <div class="content">
                  {{$cavelog->modification_note}}
                </div>
              </div>
            </div>

          @endforeach
      </div>
      <div class="column">
        <h3 class="title is-3">{{ __('varcave.homepage.featuredCave')}}</h3>
        <div class="card is-link">
          <div class="card-content">
            <div class="media">
              <div class="media-left">
                <figure class="image is-48x48">
                  <img
                    src="{{ asset('img/logo_cavite_150x150.png') }}"
                    alt="small cave logo"
                  />
                </figure>
              </div>
              <a href="{{ route('varcave.caves.show', ['uuid' => $featuredCave->uuid] ) }}" target="_blank"><div class="media-content ">
                <p class="title is-5 has-text-weight-semibold">{{ $featuredCave->name }}</p>
                <p class="subtitle is-7">{{$featuredCave->created_at}}</p>
              </div></a>
            </div>

            <div class="content">
              {{ \Illuminate\Support\Str::limit($featuredCave->description, 160)}}
              <div class="grid">
                @foreach($featuredCave->caveFiles as $files)
                  @if($files['file_type'] == 'photos')
                    <div class="cell">
                      <img src="{{ Storage::url($files->file_path) }}"></img>
                    </div>  
                  @endif
                @endforeach
                
              </div>
            </div>
          </div>
        </div>
        
      </div>   
    </div>
  </div>
</section>

@include('varcave.template.footer')
