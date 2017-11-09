import { mount } from 'vue-test-utils'
import expect from 'expect'
import ExampleComponent from '../../resources/assets/js/components/Example.vue'

describe('Example', () => {

  let component

  beforeEach(() => {
    component = mount(ExampleComponent)
  })

  it('contains default label', () => {
    expect(component.html()).toContain('Example Component')
  })

})