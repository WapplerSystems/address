/**
 * Module: TYPO3/CMS/MySitePackage/Backend/FormEditor/ViewModel
 */
define(['jquery',
    'TYPO3/CMS/Form/Backend/FormEditor/Helper'
], function($, Helper) {
    'use strict';

    return (function($, Helper) {

        /**
         * @private
         *
         * @var object
         */
        var _formEditorApp = null;


        /**
         * @private
         *
         * @var object
         */
        var _configuration = {};


        /**
         * @private
         *
         * @return object
         */
        function getFormEditorApp() {
            return _formEditorApp;
        };

        /**
         * @private
         *
         * @return object
         */
        function getPublisherSubscriber() {
            return getFormEditorApp().getPublisherSubscriber();
        };

        /**
         * @private
         *
         * @return object
         */
        function getUtility() {
            return getFormEditorApp().getUtility();
        };

        /**
         * @private
         *
         * @param object
         * @return object
         */
        function getHelper() {
            return Helper;
        };

        /**
         * @private
         *
         * @return object
         */
        function getCurrentlySelectedFormElement() {
            return getFormEditorApp().getCurrentlySelectedFormElement();
        };

        /**
         * @private
         *
         * @param mixed test
         * @param string message
         * @param int messageCode
         * @return void
         */
        function assert(test, message, messageCode) {
            return getFormEditorApp().assert(test, message, messageCode);
        };

        /**
         * @private
         *
         * @return void
         * @throws 1491643380
         */
        function _helperSetup() {
            assert('function' === $.type(Helper.bootstrap),
                'The view model helper does not implement the method "bootstrap"',
                1491643380
            );


            Helper.bootstrap(getFormEditorApp());
        };

        /**
         * @private
         *
         * @return void
         */
        function _subscribeEvents() {

            getPublisherSubscriber().subscribe('view/stage/abstract/render/template/perform', function(topic, args) {
                _renderTemplateDispatcher(args[0],args[1]);
            });


        };

        /**
         * @private
         *
         * @return void
         */
        function _renderTemplateDispatcher(formElement, template) {
            switch (formElement.get('type')) {
                case 'ContactLabel':
                    getFormEditorApp().getViewModel().getStage().renderSimpleTemplateWithValidators(formElement, template);
                    break;

            }
        };


            /**
         * @public
         *
         * @param object formEditorApp
         * @return void
         */
        function bootstrap(formEditorApp) {
            _formEditorApp = formEditorApp;
            _helperSetup();
            _subscribeEvents();
        };

        /**
         * Publish the public methods.
         * Implements the "Revealing Module Pattern".
         */
        return {
            bootstrap: bootstrap
        };
    })($, Helper);
});