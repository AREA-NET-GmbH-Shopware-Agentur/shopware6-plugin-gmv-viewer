import template from './an-gmv-table.html.twig';

import deDE from '../../snippet/de-DE.json';
import enGB from '../../snippet/en-GB.json';

const { Criteria } = Shopware.Data;

export default Shopware.Component.wrapComponentConfig('an-gmv-table', {
    inject: ['repositoryFactory'],

    template: template,

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    data: function () {
        return {
            result: null,
            isLoading: true,
            error: null
        }
    },

    computed: {
        gmvRepository() {
            return this.repositoryFactory.create('areanet_gmv');
        }
    },

    created() {
        this.fetchLastThreeYearsGmv();
    },

    methods() {
        fetchLastThreeYearsGmv : async () => {
            this.isLoading = true;
            this.error = null;
            const currentYear = new Date().getFullYear();
            const yearstoFetch = [currentYear - 2, currentYear - 1, currentYear];

            const criteria = new Criteria();
            criteria.adddFilter(Criteria.equalsAny('year', yearstoFetch));
            criteria.addSorting(Criteria.sort('year', 'DESC'));

            try{
                this.result = await this.gmvRepository.search(criteria);
            } catch (e) {
                this.error = 'Fehler beim Laden der GMV-Daten.';
                console.error(this.error, e);
            } finally {
                this.isLoading = false;
            }
        }
    }

});