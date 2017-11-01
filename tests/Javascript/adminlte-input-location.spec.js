import { mount } from 'vue-test-utils'
import expect from 'expect'
import AdminlteInputLocation from '../../../relationships/resources/assets/js/personal-data/AdminlteInputLocation.vue'

describe('AdminlteInputLocation', () => {
  let component

  beforeEach(() => {
    component = mount(AdminlteInputLocation)
  })

  it('contains default label', () => {
    expect(component.html()).toContain('Location')
  })

  it('label can be setted', () => {
    const component = mount(AdminlteInputLocation, {
      slots: {
        label: '<div>Birth place</div>'
      }
    })

    expect(component.html()).toContain('Birth place')
  })

  it('contains default id', () => {
    let date = component.find('input')
    expect(date.element.id).toBe('location')
  })

  it('contains default name', () => {
    let date = component.find('input')
    expect(date.element.name).toBe('location')
  })

  it('name and id could be setted', () => {
    component.setProps({
      id: 'birthplace_id',
      name: 'birthplace_id',
    })
    let date = component.find('input')
    expect(date.element.id).toBe('birthplace_id')
    expect(date.element.name).toBe('birthplace_id')
  })

  it('accepts a location', () => {
    expect(component.contains('input')).toBe(true)
  })

  it('can be disabled', () => {
    component.setProps({ disabled: true })

    let date = component.find('input')

    expect(date.element.disabled).toBe(true)

  })
})