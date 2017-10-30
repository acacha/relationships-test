
    var testsContext = require.context("../../tests/Javascript", false);

    var runnable = testsContext.keys();

    runnable.forEach(testsContext);
    