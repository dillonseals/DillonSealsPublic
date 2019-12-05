# https://docs.djangoproject.com/en/2.2/intro/tutorial01/

from django.urls import  path

from . import views

urlpatterns = [
    path('', views.index, name='index'),
    path('product/', views.productPage, name='productPage'),
    path('wish/', views.wishPage, name='wishPage'),
    path('cart/', views.cartPage, name='cartPage'),
    path('search/', views.searchPage, name='searchPage'),
    path('login/', views.loginPage, name='loginPage'),
    path('redirect/', views.redirectPage, name='redirectPage')
]