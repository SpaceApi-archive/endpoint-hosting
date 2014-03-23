window.onload = function () {

  var json = {
    test: "adf"
  }

  new jsoneditor.JSONEditor(
    document.getElementById('jsoneditor-tree'),
    {
      mode: "tree",
      modes: [ // allowed modes
      ]
    },
    json
  );

  new jsoneditor.JSONEditor(
    document.getElementById('jsoneditor-code'),
    {
      mode: "code",
      modes: [ // allowed modes

      ]
    },
    json
  );


}
