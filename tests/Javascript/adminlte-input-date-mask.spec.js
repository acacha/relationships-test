import { mount } from 'vue-test-utils'
import expect from 'expect'
import AdminlteInputDateMask from '../../../relationships/resources/assets/js/personal-data/AdminlteInputDateMask.vue'

describe('AdminlteInputDateMask', () => {
  let component

  beforeEach(() => {
    component = mount(AdminlteInputDateMask)
  })

  it('contains default label', () => {
    expect(component.html()).toContain('Date')
  })

  it('label can be setted', () => {


    expect(component.html()).toContain('Birth date')
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
    expect(component.vm.localeDate).toBe('02-03-9178')

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