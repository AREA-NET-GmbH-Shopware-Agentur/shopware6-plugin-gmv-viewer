import template from './gmv-table.html.twig';

const { Component } = Shopware;

Component.register('gmv-table', {
    template,

    data() {
        return {
            tableData: [
                { id: 1, name: 'Item A', value: 10 },
                { id: 2, name: 'Item B', value: 25 },
            ],
            columns: [
                { key: 'id', label: 'ID' },
                { key: 'name', label: 'Name' },
                { key: 'value', label: 'Value' },
            ],
        };
    },
});