import './index.css';

// Respect prefers-reduced-motion: pause autoplaying background video,
// resume if the preference changes back.
const mq = window.matchMedia('(prefers-reduced-motion: reduce)');

function applyMotionPreference() {
	document.querySelectorAll('.hero-block video.hero__media').forEach((video) => {
		if (mq.matches) {
			video.pause();
		} else if (video.paused) {
			video.play().catch(() => {});
		}
	});
}

applyMotionPreference();
mq.addEventListener('change', applyMotionPreference);
