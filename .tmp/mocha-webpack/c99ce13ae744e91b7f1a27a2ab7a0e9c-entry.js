
    var testsContext = require.context("../../relationships/tests/Javascript", false);

    var runnable = testsContext.keys();

    runnable.forEach(testsContext);
    