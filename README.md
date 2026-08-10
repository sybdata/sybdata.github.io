# sybdata.github.io

The compact landing page for [sybdata.github.io](https://sybdata.github.io/).

The site is built with Jekyll 4 and deployed through GitHub Pages Actions. It intentionally contains only public website assets; experiments, credentials, database dumps, archives, and certificates do not belong in this repository.

## Local development

```sh
bundle install
bundle exec jekyll serve --livereload
```

Open <http://127.0.0.1:4000/>.

## Deployment

Pull requests run the same production build used for deployment. After the workflow is merged, set **Settings → Pages → Build and deployment → Source** to **GitHub Actions** once. Pushes to `master` will then publish automatically.
