import './index.css';
import { store, getContext } from '@wordpress/interactivity';

store('tabs', {
    actions: {
        selectTab: () => {
            const ctx = getContext();
            ctx.activeTab = ctx.tabIndex;
        },
    },
    callbacks: {
        isActive: () => {
            const ctx = getContext();
            return ctx.activeTab === ctx.tabIndex;
        },
        isHidden: () => {
            const ctx = getContext();
            return ctx.activeTab !== ctx.tabIndex;
        },
    },
});
