import { defineStore } from 'pinia';
import { fetchJson } from '../services/api';

export const useSettingsStore = defineStore('settings', {
    state: () => ({
        item: {},
        loaded: false,
    }),
    actions: {
        async load() {
            if (this.loaded) {
                return;
            }

            try {
                const response = await fetchJson('/api/settings');
                this.item = response.item ?? {};
            } catch (error) {
                this.item = {};
            } finally {
                this.loaded = true;
            }
        },
    },
});
