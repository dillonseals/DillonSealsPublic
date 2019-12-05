# https://docs.djangoproject.com/en/2.2/intro/tutorial01/

from django.shortcuts import render
from django.http import HttpResponse, HttpResponseRedirect
from django.template import loader
from django.contrib.auth.models import User
from django.contrib.auth import authenticate
from django.urls import reverse
from django.db.models import Avg

from .models import Product
from .models import Wish
from .models import Cart
from .models import Review

# Create your views here.

# homepage
def index(request):
    # get all products from db
    productList = Product.objects.all()
    # if user logged in
    if request.session.get('user', None) is not None:
        context = {'productList': productList, 'user': request.session['user']}
        return render(request, 'eCommerce/index.html', context)
    # if no logged in user
    else:
        context = {'productList': productList, 'user': 'Guest'}
        return render(request, 'eCommerce/index.html', context)


# product view page
def productPage(request):
    # if user logged in
    if request.session.get('user', None) is not None:
        # get product with passed id
        product = Product.objects.get(id=request.POST.get('productID', None))
        reviews = Review.objects.all().filter(product=product.name)
        # calculate avg review score
        """ for ratings in reviews.objects.number:
            count += 1
            final += int(ratings)
        product.score = final / count
        print('testing') """
        context = {'product': product, 'reviews': reviews, 'user': request.session['user']}
        return render(request, 'eCommerce/productPage.html', context)
    # user not logged in
    else:
        # get product with passed id
        product = Product.objects.get(id=request.POST.get('productID', None))
        reviews = Review.objects.all().filter(product=product.name)
        context = {'product': product, 'reviews': reviews}
        return render(request, 'eCommerce/productPage.html', context)


# wish list
def wishPage(request):
    if request.session.get('user', None) is not None:
        if request.POST.get('wish', None) is not None:
            # check if product already on wish list
            #if Wish.objects.get(stuff=request.POST.get('wish', None), user=request.session['user']) is None:
                Wish.objects.create(user=request.session['user'], stuff=request.POST['wish'])
        productList = Wish.objects.all().filter(user=request.session['user'])
        context = {'productList': productList, 'user': request.session['user']}
        return render(request, 'eCommerce/wishPage.html', context)
    else:
        return HttpResponseRedirect(reverse('eCommerce:index'))


# shopping cart
def cartPage(request):
    if request.session.get('user', None) is not None:
        if request.POST.get('cart', None) is not None:
            # check if product already in cart
            #if Cart.objects.get(stuff=request.POST['cart'], user=request.session['user']) is None:
                Cart.objects.create(user=request.session['user'], stuff=request.POST['cart'])
        productList = Cart.objects.all().filter(user=request.session['user'])
        context = {'productList': productList, 'user': request.session['user']}
        return render(request, 'eCommerce/cartPage.html', context)
    else:
        return HttpResponseRedirect(reverse('eCommerce:index'))


# search page
def searchPage(request):
    productList = Product.objects.filter(color=request.POST['color'])
    context = {'productList': productList}
    return render(request, 'eCommerce/search.html', context)


# login page
def loginPage(request):
    # logout
    if request.session.get('user', None) is not None:
        request.session.flush()
    return render(request, 'eCommerce/loginPage.html')


# redirect page for back-end code
def redirectPage(request):
    # do the PHP thing
    # login
    if request.POST.get('login', False) == 'login':
        user = authenticate(username=request.POST['username'], password=request.POST['password'])
        if user is not None:
            # A backend authenticated the credentials
            request.session['user'] = request.POST['username']
            return HttpResponseRedirect(reverse('eCommerce:index'))
        else:
            # No backend authenticated the credentials
            # note - does not display error message
            return HttpResponseRedirect(reverse('eCommerce:loginPage'))


    # register a user
    if request.POST.get('register', False) == 'register':
        username = request.POST['username']
        password = request.POST['password']
        user = User.objects.create_user(username=username, password=password)
        user.save()
        request.session['user'] = user
        return HttpResponseRedirect(reverse('eCommerce:index'))


    # leave a review
    if request.POST.get('review', False) == 'review':
        Review.objects.create(
            reviewer=request.session['user'],
            product=request.POST['productName'],
            number=request.POST['rating'],
            text=request.POST['reviewText'],
        )
        return HttpResponseRedirect(reverse('eCommerce:index'))
