import './index.css';
import { getModuleData } from '@shared';

/**
 * Constant scroll speed regardless of how many logos the editor added.
 *
 * The CSS animation runs for a fixed duration, so a strip of 5 logos crawls
 * and a strip of 40 races. Duration has to be derived from the rendered track
 * width, which only the browser knows — but the speed itself is an editorial
 * decision, so it comes from the server through this block's script module
 * payload (see functions.php → script_module_data_marquee).
 */
const { speed = 60 } = getModuleData('marquee');

function applyDuration(block) {
	const track = block.querySelector('.marquee-track');

	if (!track) {
		return;
	}

	// The track holds the logo set twice; the keyframe travels one set.
	const distance = track.scrollWidth / 2;

	if (!distance) {
		return;
	}

	block.style.setProperty('--marquee-duration', `${distance / speed}s`);
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.marquee-block').forEach(applyDuration);
});
