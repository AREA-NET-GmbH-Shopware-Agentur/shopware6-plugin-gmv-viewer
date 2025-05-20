import template from './an-gmv-table.html.twig';

const { Criteria } = Shopware.Data;

Shopware.Component.register('an-gmv-table', {
    inject: ['repositoryFactory'],

    template: template,

    data: function () {
        return {
            result: null,
            isLoading: true,
            error: null
        }
    },

    computed: {
        currencyFilter() {
            return Shopware.Filter.getByName('currency');
        },

        gmvRepository() {
            return this.repositoryFactory.create('areanet_gmv');
        }
    },

    created() {
        this.fetchLastThreeYearsGmv();
    },

    methods: {
        async fetchLastThreeYearsGmv() {
            this.isLoading = true;
            this.error = null;
            const currentYear = new Date().getFullYear();
            const yearstoFetch = [currentYear - 2, currentYear - 1, currentYear];

            const criteria = new Criteria();
            criteria.addFilter(Criteria.equalsAny('year', yearstoFetch));
            criteria.addSorting(Criteria.sort('year', 'DESC'));

            try{
                this.result = await this.gmvRepository.search(criteria);
            } catch (e) {
                this.error = 'Fehler beim Laden der GMV-Daten.';
                console.error(this.error, e);
            } finally {
                this.isLoading = false;
            }

            console.log(this.error);
            console.log(this.result);
        }
    }

});
