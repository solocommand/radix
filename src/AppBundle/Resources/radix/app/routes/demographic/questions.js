import Ember from 'ember';

const { inject: { service }, Route } = Ember;

export default Route.extend({

    query: service('model-query'),

    model: function() {
        return this.get('query').execute('question');
    },

    actions: {
        loadTabs: function() {
            return [
                { key : 'general', text : 'General', icon : 'ion-document',            template : 'demographic/questions/-general', active : true },
                { key : 'answers', text : 'Choices', icon : 'ion-android-list',        template : 'demographic/questions/-answers' },
                { key : 'info',    text : 'Info',    icon : 'ion-information-circled', template : 'demographic/questions/-info' },
            ];
        },
        recordAdded: function() {
            this.refresh();
        }
    }

});
