const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    username: props.user.username,
    city: props.user.city,
    country: props.user.country,
});

const submitProfileForm = () => {
    profileForm.put('/user/profile-information', {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh the page to update the user's setup status
            window.location.reload();
        },
    });
};

const handleReaction = (type, beatId) => {
    form.beat_id = beatId
    form.reaction_type = type

    form.post(route('reaction.add'), {
        preserveScroll: true,
        onSuccess: (response) => {
            // Update the reaction counts with the response from the server
            if (response?.props?.flash?.reactions) {
                reactionCounts.value[beatId] = response.props.flash.reactions;
            }
        },
        onError: (errors) => {
            console.error('Reaction error:', errors)
        }
    })
}
