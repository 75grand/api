<h2 class="text-xl text-gray-500 max-w-[30rem] [text-wrap:balance] [&>*]:transition-all [&>*]:whitespace-nowrap">
    <span data-screenshot="menu">Menus</span>,
    <span data-screenshot="calendar-sports"><span class="opacity-50">m</span>events</span>,
    <span data-screenshot="marketplace">marketplace</span>,
    <span data-screenshot="moodle">Moodle</span>,
    <span data-screenshot="hours"><span class="opacity-50">m</span>building hours</span>,
    <span data-screenshot="macpass">MacPass</span>,
    <span data-screenshot="mailbox-combination">mailbox info</span>,
    and <span data-screenshot="home">more</span>
</h2>

<script>
    function getScreenshotUrl(name) {
        return `{{ url('/assets/screenshots') }}/${name}.png`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const image = document.getElementById('phone-image');
        const features = [...document.querySelectorAll('[data-screenshot]')];

        let index = 0;
        let prev = null;

        // Preload images
        features
            .map(x => x.src)
            .map(getScreenshotUrl)
            .forEach(x => new Image(x));

        nextWord();
        setInterval(nextWord, 1500);

        function nextWord() {
            const feature = features[index];

            if(prev) prev.classList.remove('text-accent', 'font-bold');
            feature.classList.add('text-accent', 'font-bold');

            image.src = getScreenshotUrl(feature.dataset.screenshot);

            prev = feature;
            index = (index + 1) % features.length;
        }
    });
</script>