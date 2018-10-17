require('nightwatch-cucumber')({
  cucumberArgs: [
    '--format', 'node_modules/cucumber-pretty',
    'features'
  ]
});

module.exports = {
    output_folder: false,
    custom_commands_path: '',
    custom_assertions_path: '',
    page_objects_path: '',
    live_output: false,
    disable_colors: false,
    selenium: {
        start_process: false,
        server_path: require('selenium-server').path,
        host: '127.0.0.1',
        port: 4444,
        cli_args: {
            'webdriver.chrome.driver': require('chromedriver').path,
        },
    },
    test_settings: {
        default: {
            launch_url: 'http://localhost:3000',
            silent: true,
            selenium_port: 4444,
            desiredCapabilities: {
                javascriptEnabled: true,
                acceptSslCerts: true,
                browserName: 'chrome',
                loggingPrefs: {
                    browser: 'ALL',
                },
                chromeOptions: {
                    args: [
                        '--disable-background-networking',
                        '--disable-default-apps',
                        '--disable-extensions',
                        '--disable-gpu',
                        '--disable-sync',
                        '--disable-translate',
                        '--headless',
                        '--hide-scrollbars',
                        '--metrics-recording-only',
                        '--mute-audio',
                        '--no-first-run',
                        '--no-sandbox',
                        '--safebrowsing-disable-auto-update',
                    ]
                },
            },
            end_session_on_fail: false,
            skip_testcase_on_fail: false,
            screenshots: {
                enabled: true,
                on_error: true,
                on_failure: true,
                path: 'screenshots',
            },
        },
        local: {
            selenium: {
                start_process: true,
            },
        },
        docker: {
            launch_url: 'http://client:3000',
            selenium_host: 'selenium',
        },
        ci: {
            selenium_host: '127.0.0.1',
            screenshots : {
                enabled: false,
                path: '',
            },
        },
    },
};
