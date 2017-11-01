import { mount } from 'vue-test-utils'
import expect from 'expect'
import AdminlteInputGender from '../../../relationships/resources/assets/js/personal-data/AdminlteInputGender.vue'

describe('AdminlteInputGender', () => {
  let component

  beforeEach(() => {
    component = mount(AdminlteInputGender)
  })

  it('contains default label', () => {
    expect(component.html()).toContain('Gender')
  })

  it('label can be setted', () => {
    const component = mount(AdminlteInputGender, {
      slots: {
        label: '<div>Sex</div>'
      }
    })

    expect(component.html()).toContain('Sex')
  })

  it('contains default id', () => {
    let date = component.find('input')
    expect(date.element.id).toBe('gender')
  })

  it('contains default name', () => {
    let date = component.find('input')
    expect(date.element.name).toBe('gender')
  })

  it('name and id could be setted', () => {
    component.setProps({
      id: 'sex',
      name: 'sex',
    })
    let date = component.find('input')
    expect(date.element.id).toBe('sex')
    expect(date.element.name).toBe('sex')
  })

  it('accepts a gender', () => {
    expect(component.contains('input')).toBe(true)
  })

  it('can be disabled', () => {
    component.setProps({ disabled: true })

    let date = component.find('input')

    expect(date.element.disabled).toBe(true)

  })
})