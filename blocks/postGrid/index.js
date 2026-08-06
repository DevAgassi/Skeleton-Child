import './index.css';
import { store, getContext } from '@wordpress/interactivity';

store('postGrid', {
    actions: {
        setFilter: () => {
            const ctx = getContext();
            ctx.activeFilter = ctx.filterSlug;
        },
    },
    callbacks: {
        isItemHidden: () => {
            const ctx = getContext();
            return ctx.activeFilter !== 'all' && !ctx.itemTerms.includes(ctx.activeFilter);
        },
        isFilterActive: () => {
            const ctx = getContext();
            return ctx.activeFilter === ctx.filterSlug;
        },
    },
});
