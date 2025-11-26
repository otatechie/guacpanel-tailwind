<script setup>

import GoogleIcon from "./Icons/GoogleIcon.vue";
import FacebookIcon from "./Icons/FacebookIcon.vue";
import GitHubIcon from "./Icons/GitHubIcon.vue";
import LinkedInIcon from "./Icons/LinkedInIcon.vue";

const props = defineProps({
  providersConfig: {
    type: Object,
    required: false,
    default: () => ({
      button_text: '',
      providers: [],
    }),
  },
})

const redirect = (provider) => {
  window.location.href = route("social.redirect", { provider });
};

const providerClasses = (provider) => {
  // if (provider == 'google') {
  //   return 'bg-red-500 hover:bg-red-600 hover:text-white text-white dark:bg-red-600 dark:hover:bg-red-700';
  // }
  // if (provider == 'facebook') {
  //   return 'bg-blue-600 hover:bg-blue-700 hover:text-white text-white dark:bg-blue-700 dark:hover:bg-blue-800';
  // }
  // if (provider == 'github') {
  //   return 'bg-gray-900 hover:bg-gray-700 hover:text-white text-white dark:border-white dark:bg-transparent dark:hover:bg-gray-900';
  // }
  // if (provider == 'linkedin') {
  //   return 'bg-blue-700 hover:bg-blue-800 hover:text-white text-white dark:bg-blue-800 dark:hover:bg-blue-900';
  // }

  return null;
};

const providerIcon = (provider) => {
  if (provider === 'google') {
    return GoogleIcon
  }
  if (provider === 'facebook') {
    return FacebookIcon
  }
  if (provider === 'github') {
    return GitHubIcon
  }
  if (provider === 'linkedin') {
    return LinkedInIcon
  }

  return null
}

</script>

<template>
  <div class="flex flex-col gap-4">
    <template
      v-for="(provider, index) in providersConfig.providers"
      :key="index"
    >
      <button
        type="button"
        variant="outline"
        :class="providerClasses(index)"
        :tabIndex="5"
        @click="redirect(index)"
        class="flex items-center justify-center gap-2 btn-secondary text-sm transition-colors cursor-pointer"
      >
        <component
          :is="providerIcon(index)"
          class="size-4"
        />
        {{ providersConfig.button_text.replace("{provider}", index) }}
      </button>
    </template>
  </div>
</template>
