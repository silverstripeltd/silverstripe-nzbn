import jQuery from 'jquery'
import NZBNService from './NZBNService'

jQuery($ => {
  const $lists = $('.js-nzbn-list')
  const service = new NZBNService()

  $(window).on('keydown', function (e) {
    if (e.which === 27) {
      $lists.hide()
    }
  })

  $(document).on('mouseup', function (e) {
    if (!$lists.is(e.target) && $lists.has(e.target).length === 0) {
      $lists.hide()
    }
  })

  $('.nzbn-lookup').each(function () {
    const $this = $(this)
    const $input = $this.find('input[type=text]')
    const $button = $this.find('.js-nzbn-button')
    const $list = $this.find('.js-nzbn-list')

    $button.on('click', function (e) {
      e.preventDefault()

      const $this = $(this)
      const $container = $this.closest('.nzbn-lookup')
      const $field = $container.find('input[type=text]')
      const query = $.trim($field.val())

      if (query.length > 0) {
        setLoadingState(true)

        service.search(query).then(data => {
          showOptionsList(data ? data.items : [])
          setLoadingState(false)
        })
      }
    })

    $list.on('click', '.nzbn-item', function () {
      const $this = $(this)
      const nzbn = $this.data('nzbn')
      const fieldMap = $input.data('nzbn')
      const value = $this.text()

      $list.hide()
      $input.val(value)

      setLoadingState(true)

      service.get(nzbn).then(data => {
        populateForm(fieldMap, data)
        setLoadingState(false)
      })
    })

    function setLoadingState(loading) {
      $this.toggleClass('loading', loading)
    }

    function showOptionsList(options) {
      $lists.hide().empty()

      if (options.length > 0) {
        $list.show()

        options.map(item => {
          $(`<li class="nzbn-item">${item.entityName}</li>`).data('nzbn', item.nzbn).appendTo($list)
        })
      }
    }

    function populateForm(fieldMap, data) {
      $.each(fieldMap, function (fieldKey, dataKey) {
        const $input = $(`[name="${fieldKey}"]`)
        const value = data[dataKey]

        if ($input.hasClass('dropdown')) {
          $input.val(value)
          $input.find('option').prop('selected', false)
          $input.find(`option[value="${value}"]`).prop('selected', true)
        } else {
          $input.val(value)
        }

        if ($.trim(value) !== '') {
          $input.addClass('valid')
        }
      })
    }
  })
})
