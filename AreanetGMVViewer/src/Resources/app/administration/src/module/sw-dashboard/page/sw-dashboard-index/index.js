import { Component } from '@shopware-ag/admin-module-sdk';
import './../../../../component/gmv-table/';

Component.override('sw-dashboard-index', (original) => {
    return {
        ...original,

        computed: {
            dashboardModules() {
                const originalModules = original.computed.dashboardModules.call(this);

                originalModules.unshift({
                    component: 'gmv-table',
                    name: 'gmv-table-card',
                    gridColumns: 12,
                });

                return originalModules;
            },
        },
    };
});