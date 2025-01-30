import { defineStore } from 'pinia'

export const useAudioStore = defineStore('audio', {
    state: () => ({
        currentBeat: null,
        isPlaying: false,
        progress: 0,
        currentTime: 0,
        duration: 0,
        volume: 1,
        isMuted: false,
        isRepeat: false,
        audioElement: null,
        isFollowing: false,
        waveSurferInstance: null,
    }),

    getters: {
        isCurrentBeat: (state) => {
            return (beatId) => state.currentBeat?.id === beatId;
        }
    },

    actions: {
        initializeAudio() {
            this.audioElement = new Audio();
            this.audioElement.volume = this.volume;
            this.audioElement.muted = this.isMuted;
            this.audioElement.loop = this.isRepeat;

            // Add event listeners
            this.audioElement.addEventListener('timeupdate', this.handleTimeUpdate);
            this.audioElement.addEventListener('ended', this.handleEnded);
            this.audioElement.addEventListener('loadedmetadata', this.handleLoadedMetadata);
        },

        async playBeat(beat) {
            this.currentBeat = beat;
            if (this.waveSurferInstance) {
                const audioUrl = beat?.media?.audio[0]?.original_url || beat?.media?.audio;
                if (audioUrl) {
                    await this.waveSurferInstance.load(audioUrl);
                    this.waveSurferInstance.play();
                    this.isPlaying = true;
                }
            }
        },

        togglePlay() {
            if (!this.waveSurferInstance) return;

            try {
                if (this.waveSurferInstance.isPlaying()) {
                    this.waveSurferInstance.pause();
                    this.isPlaying = false;
                } else {
                    this.isPlaying = true; // Update state immediately
                    this.waveSurferInstance.play()
                        .catch(error => {
                            console.error('Error playing audio:', error);
                            this.isPlaying = false;
                        });
                }
            } catch (error) {
                console.error('Error toggling play/pause:', error);
                this.isPlaying = false;
            }
        },

        handleTimeUpdate() {
            this.currentTime = this.audioElement.currentTime;
            this.progress = (this.audioElement.currentTime / this.audioElement.duration) * 100;
        },

        handleEnded() {
            if (!this.isRepeat) {
                this.isPlaying = false;
                this.progress = 0;
            }
        },

        handleLoadedMetadata() {
            this.duration = this.audioElement.duration;
        },

        setVolume(value) {
            this.volume = value;
            if (this.audioElement) {
                this.audioElement.volume = value;
            }
        },

        toggleMute() {
            this.isMuted = !this.isMuted;
            if (this.audioElement) {
                this.audioElement.muted = this.isMuted;
            }
        },

        toggleRepeat() {
            this.isRepeat = !this.isRepeat;
            if (this.audioElement) {
                this.audioElement.loop = this.isRepeat;
            }
        },

        cleanup() {
            if (this.audioElement) {
                this.audioElement.removeEventListener('timeupdate', this.handleTimeUpdate);
                this.audioElement.removeEventListener('ended', this.handleEnded);
                this.audioElement.removeEventListener('loadedmetadata', this.handleLoadedMetadata);
            }
        },

        updateBeatStats(beatId, stats) {
            if (this.currentBeat?.id === beatId) {
                this.currentBeat.stats = { ...this.currentBeat.stats, ...stats };
                this.currentBeat = { ...this.currentBeat };
            }
        },

        setWaveSurfer(instance) {
            this.waveSurferInstance = instance;

            // Add event listeners to update state
            this.waveSurferInstance.on('play', () => {
                this.isPlaying = true;
            });

            this.waveSurferInstance.on('pause', () => {
                this.isPlaying = false;
            });

            this.waveSurferInstance.on('finish', () => {
                if (!this.isRepeat) {
                    this.isPlaying = false;
                }
            });

            // Add time update listeners
            this.waveSurferInstance.on('audioprocess', (currentTime) => {
                this.currentTime = currentTime;
                this.duration = this.waveSurferInstance.getDuration();
                this.progress = (currentTime / this.duration) * 100;
            });

            this.waveSurferInstance.on('ready', () => {
                this.duration = this.waveSurferInstance.getDuration();
                this.currentTime = 0;
                this.progress = 0;
            });
        },

        async play() {
            if (this.waveSurferInstance) {
                await this.waveSurferInstance.play();
                this.isPlaying = true;
            }
        }
    }
})
