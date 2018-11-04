export default function NZBNService() {
  this.baseURL = document.location.protocol + '//' + document.location.hostname + '/NZBNLookup'
  this.headers = {
    'Method': 'GET',
    'Content-Type': 'application/json',
    'x-Requested-With': 'XMLHttpRequest',
  }
}

NZBNService.prototype.get = function (nzbn) {
  const url = this.baseURL + '?NZBN=' + nzbn
  const request = new Request(url, { headers: this.headers })

  return fetch(request)
    .then(function (response) {
      return response.json()
    })
    .catch(function (error) {
      console.log(error)
    })
}

NZBNService.prototype.search = function (query) {
  const url = this.baseURL + '/search?query=' + query
  const request = new Request(url, { headers: this.headers })

  return fetch(request)
    .then(function (response) {
      return response.json()
    })
    .catch(function (error) {
      console.log(error)
    })
}
