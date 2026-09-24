@props(['src', 'poster'])
<div
    x-data="{
          playing: false,
          muted: false,
          progress: 0,
          duration: 0,
          showControls: true,
          hideTimer: null,
          togglePlay() {
            this.playing ? this.$refs.vid.pause() : this.$refs.vid.play();
          },
          toggleMute() {
            this.muted = !this.muted;
            this.$refs.vid.muted = this.muted;
          },
          onTimeUpdate() {
            this.progress = (this.$refs.vid.currentTime / this.$refs.vid.duration) * 100 || 0;
          },
          seek(e) {
            const rect = this.$refs.bar.getBoundingClientRect();
            const pct = (e.clientX - rect.left) / rect.width;
            this.$refs.vid.currentTime = pct * this.$refs.vid.duration;
          },
          wake() {
            this.showControls = true;
            clearTimeout(this.hideTimer);
            this.hideTimer = setTimeout(() => { if (this.playing) this.showControls = false }, 2200);
          }
        }"
    @mousemove="wake()"
    class="relative aspect-video w-full overflow-hidden rounded-3xl bg-black group"
>
    <video
        x-ref="vid"
        src="{{ $src }}"
        poster="{{ $poster }}"
        class="absolute inset-0 w-full h-full object-cover"
        playsinline
        @click="togglePlay()"
        @play="playing = true"
        @pause="playing = false"
        @timeupdate="onTimeUpdate()"
        @loadedmetadata="duration = $refs.vid.duration"
    ></video>

    <!-- Center Play Button -->
    <button
        type="button"
        @click="togglePlay()"
        x-show="!playing"
        x-transition.opacity
        class="absolute inset-0 m-auto w-20 h-20 rounded-full bg-white/90 hover:bg-white flex items-center justify-center transition-colors"
        aria-label="{{ __('Play video') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7 4v16l13 -8z" fill="black" stroke="none"/>
        </svg>
    </button>

    <!-- Bottom Gradient -->
    <div
        class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/70 to-transparent pointer-events-none transition-opacity duration-300"
        :class="showControls || !playing ? 'opacity-100' : 'opacity-0'"
    ></div>

    <!-- Controls Bar -->
    <div
        x-show="showControls || !playing"
        x-transition.opacity
        class="absolute bottom-0 inset-x-0 p-6 flex items-center gap-4 text-white"
    >
        <button
            type="button"
            @click="togglePlay()"
            class="shrink-0"
            aria-label="{{ __('Play or pause video') }}"
        >
            <svg x-show="!playing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M7 4v16l13 -8z"/></svg>
            <svg x-show="playing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16" rx="1" fill="white" stroke="none"/><rect x="14" y="4" width="4" height="16" rx="1" fill="white" stroke="none"/></svg>
        </button>

        <div
            x-ref="bar"
            @click="seek($event)"
            class="relative flex-1 h-[2px] bg-white/30 rounded-full cursor-pointer"
        >
            <div class="absolute top-0 left-0 h-full bg-white rounded-full" :style="`width: ${progress}%`"></div>
            <div
                class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full transition-transform group-hover:scale-100 scale-0"
                :style="`left: calc(${progress}% - 6px)`"
            ></div>
        </div>

        <button
            type="button"
            @click="toggleMute()"
            class="shrink-0"
            aria-label="{{ __('Mute or unmute video') }}"
        >
            <svg x-show="!muted" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 8a5 5 0 0 1 0 8" /><path d="M17.7 5a9 9 0 0 1 0 14" /><path d="M6 15h-2a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h2l3.5 -4.5a.8 .8 0 0 1 1.5 .5v14a.8 .8 0 0 1 -1.5 .5l-3.5 -4.5" />
            </svg>
            <svg x-show="muted" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 15h-2a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h2l3.5 -4.5a.8 .8 0 0 1 1.5 .5v14a.8 .8 0 0 1 -1.5 .5l-3.5 -4.5" /><path d="M16 10l4 4m0 -4l-4 4" />
            </svg>
        </button>
    </div>
</div>
