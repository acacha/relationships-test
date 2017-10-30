import { mount } from 'vue-test-utils'
import expect from 'expect'
import AdminlteInputDateMask from '../../../relationships/resources/assets/js/personal-data/AdminlteInputDateMask.vue'

describe('AdminlteInputDateMask', () => {
  let component

  beforeEach(() => {
    window.acacha_relationships = {}
    window.acacha_relationships.config = {
      locale: 'ca',
      momentDateFormat : 'DD-MM-YYYY' ,
      dateMask: 'dd/mm/yyyy',
      laravelDateFormat : 'YYYY-MM-DD h:mm:ss'
    }
    component = mount(AdminlteInputDateMask)
  })

  it('contains default label', () => {
    expect(component.html()).toContain('Date')
  })

  it('label can be setted', () => {
    const component = mount(AdminlteInputDateMask, {
      slots: {
        label: '<div>Birth date</div>'
      }
    })

    expect(component.html()).toContain('Birth date')
  })

  it('contains default id', () => {
    let date = component.find('input')
    expect(date.element.id).toBe('date')
  })

  it('contains default name', () => {
    let date = component.find('input')
    expect(date.element.name).toBe('date')
  })

  it('name and id could be setted', () => {
    component.setProps({
      id: 'birthdate',
      name: 'birthdate',
    })
    let date = component.find('input')
    expect(date.element.id).toBe('birthdate')
    expect(date.element.name).toBe('birthdate')
  })

  it('accepts a date', () => {
    expect(component.contains('input')).toBe(true)
  })

  it('can be disabled', () => {
    component.setProps({ disabled: true })

    let date = component.find('input')

    expect(date.element.disabled).toBe(true)

  })

  it('changes the date', () => {
    let date = component.find('input')

    date.element.value = '02031978'
    date.trigger('input')
    date.trigger('change')

    expect(component.vm.newDate).toBe('1978-03-02 12:00:00')
    // expect(component.vm.localeDate).toBe('02-03-1978')

  })

  it('broadcast the changed date', () => {
    let date = component.find('input')

    date.element.value = '02031978'
    date.trigger('input')
    date.trigger('change')
    expect(component.emitted().change).toBeTruthy()
    expect(component.emitted().change[0]).toEqual(['1978-03-02 12:00:00'])
  })
})