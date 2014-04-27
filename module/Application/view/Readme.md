Partials should always included as follows

```
include('subdir_1/subdir_2/.../subdir_n/template.twig')
```

The current directory is Twig's template root path which means that your template path must start with a subdirectory of this folder. As every path has always to be explicitly defined it's always clear from what location you include the template.

Example
-------

```
include('application/endpoint/index-section-create-endpoint.twig')
```